<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator\Rank;

use ScriptFUSION\Mapper\AnonymousMapping;
use ScriptFUSION\Mapper\Strategy\Callback;
use ScriptFUSION\Mapper\Strategy\Copy;
use ScriptFUSION\Porter\Provider\Steam\Resource\ScrapeAppDetails;
use ScriptFUSION\Porter\Specification\ImportSpecification;
use ScriptFUSION\Porter\Transform\Mapping\MappingTransformer;

class AppDetailsSpecification extends ImportSpecification
{
    public function __construct(int $appId)
    {
        parent::__construct(new ScrapeAppDetails($appId));

        $this->addTransformer(
            new MappingTransformer(
                new AnonymousMapping([
                    'app_name' => new Copy('name'),
                    'app_type' => new Copy('type'),
                    'release_date' => new Callback(
                        function (array $data) {
                            return $data['release_date'] ? $data['release_date']->getTimestamp() : null;
                        }
                    ),
                    'genre' => new Copy('tags->0'),
                ])
            )
        );
    }
}
