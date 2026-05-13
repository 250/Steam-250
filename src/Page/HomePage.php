<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Page;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Porter;
use ScriptFUSION\Porter\Provider\Steam\Resource\GetAppAssets;
use ScriptFUSION\Porter\Provider\Steam\Resource\ScrapeAppDetails;
use ScriptFUSION\Steam250\SiteGenerator\Database\Queries;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\AnnualRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\DiscountRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\FreeRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\GameOfTheDayRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\HiddenGemsRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\MostPlayedRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\RollingWeekRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TagRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\Top250Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\TrendRanking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Impl\UsdUnder5Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Ranking\Ranking;
use ScriptFUSION\Steam250\SiteGenerator\Tag\KeystoneTagChooser;

class HomePage extends Page implements PreviousDatabaseAware
{
    use PreviousDatabase;

    /** @var Ranking[] */
    private array $rankings;

    private int $rankingCount;

    private const APP_MEDIA_MAP = [
        TrendRanking::class => 'library_hero',
        GameOfTheDayRanking::class => 'library_hero',
        RollingWeekRanking::class => 'hero_capsule',
        MostPlayedRanking::class => 'hero_capsule',
        HiddenGemsRanking::class => 'hero_capsule',
        AnnualRanking::class => 'library_capsule',
    ];

    public function __construct(
        private readonly Connection $database,
        private readonly Connection $appMediaCache,
        private readonly Porter $porter,
        private readonly LoggerInterface $logger,
        array $rankings,
        int $rankingCount,
    ) {
        parent::__construct($database, 'index');

        $this->rankings = $rankings;
        $this->rankingCount = $rankingCount;

        $this->setTemplate('home');
    }

    public function export(): array
    {
        $rankings = array_combine(
            array_map(fn ($r) => $r->getId(), $this->rankings),
            array_map(
                fn (Ranking $ranking) =>
                    [
                        'apps' => $apps = Queries::fetchRankedList($this->database, $ranking, $this->prevDb, 10)
                            |> (fn ($apps) => $ranking instanceof TagRanking ? $apps : $this->applyKeystoneTag($apps))
                            |> (fn ($apps) => $ranking instanceof Top250Ranking ? $this->applyBlurb($apps) : $apps),
                    ] + compact('ranking')
                    + $this->fetchAppMedia($ranking, $apps),
                $this->rankings
            )
        );

        return parent::export() + compact('rankings') +
            [
                'total_games' => Queries::countGames($this->database),
                'total_rankings' => $this->rankingCount,
                'total_days' => new \DateTimeImmutable('Nov 25 2017')->diff(new \DateTime())->days,
            ]
        ;
    }

    private function applyKeystoneTag(array $apps): array
    {
        foreach ($apps as &$app) {
            $app['keystone_tag'] = KeystoneTagChooser::choose(Queries::fetchAppTags($this->database, $app['id']));
        }

        return $apps;
    }

    private function applyBlurb($apps): array
    {
        // Fetch cached blurb if available.
        if (!$blurb = $this->appMediaCache->executeQuery(
            'SELECT blurb FROM app_media WHERE app_id = ?',
            [$appId = $apps[0]['id']]
        )->fetchOne()) {
            $this->logger->info("Fetching blurb for #$appId.", ['page' => $this]);
            $result = $this->porter->importOne(new Import(new ScrapeAppDetails($appId = $apps[0]['id'])));

            $blurb = $result['blurb'];

            $this->appMediaCache->executeStatement("
                INSERT INTO app_media (app_id, blurb) VALUES (?, ?)
                    ON CONFLICT (app_id) DO UPDATE SET
                        blurb = excluded.blurb
                ",
                [$appId, $blurb]
            );
        }

        $apps[0]['blurb'] = $blurb;

        return $apps;
    }

    private function fetchAppMedia(Ranking $ranking, array $apps): array
    {
        if (!$apps || !isset(self::APP_MEDIA_MAP[$ranking::class])) {
            return [];
        }

        $mediaClass = self::APP_MEDIA_MAP[$ranking::class];

        // Check if media links are already cached.
        $cachedMedia = $this->appMediaCache->executeQuery(
            "SELECT app_id, $mediaClass FROM app_media WHERE app_id IN (?) AND $mediaClass IS NOT NULL",
            [$appIds = array_map(fn ($app) => $app['id'], $apps)],
            [ArrayParameterType::INTEGER],
        )->fetchAllKeyValue();

        if ($missing = array_diff_key(array_flip($appIds), $cachedMedia)) {
            $this->logger->info(
                "Downloading media links for \"{$ranking->getId()}\" ("
                    . implode(', ', array_keys($missing)) . ').',
                ['page' => $this]
            );

            // Download media links.
            $response = $this->porter->import(new Import(new GetAppAssets(array_keys($missing))));

            // Cache media links.
            foreach ($response as $app) {
                $this->appMediaCache->executeStatement("
                    INSERT INTO app_media (app_id, $mediaClass) VALUES (?, ?)
                        ON CONFLICT (app_id) DO UPDATE SET
                            $mediaClass = excluded.$mediaClass
                    ",
                    [$app['id'], $media[$app['id']] = $app['assets'][$mediaClass]]
                );
            }
        }

        return ['app_media' => $cachedMedia + ($media ?? [])];
    }

    public static function getRankings(): array
    {
        return [
            GameOfTheDayRanking::class,
			TrendRanking::class,
			HiddenGemsRanking::class,
			date('Y'),
			RollingWeekRanking::class,
			MostPlayedRanking::class,
			UsdUnder5Ranking::class,
            DiscountRanking::class,
            FreeRanking::class,
            'hentai',
            'co-op',
            'simulation',
            '2d',
            'gore',
            'roguelike',
            'dating_sim',
            'sandbox',
            'card_game',
            'family_friendly',
            'controller',
            'rpg',
        ];
    }
}
