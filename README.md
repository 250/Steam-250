Steam 250
=========

[![Build status][Build image]][Build]
[![Code style][Style image]][Style]

This project generates the [Steam 250][Steam 250] front-end web pages.

Getting started
---------------

### Prerequisites

To start you will need at least the version of PHP specified in [`composer.json`](composer.json) and the latest version of [Composer][Composer].

### Installation

1. Clone this repository. `git clone git@github.com:250/Steam-250.git`
1. Ensure Composer is up-to-date. `composer self`
1. Install dependencies. `composer i`

Note that cloning might take a little while because we push the built pages back to the `gh-pages` branch, so you're also cloning every version of the site ever built!

### Usage

After installation `bin/generate` can be run: this is the main entry point into the application, used to generate the website. Running the command without arguments will show usage information. Two commands are currently available.

* page – generates a single page.
* site – generates the entire website (every page).

The defaults for each command are intended to be suitable for development.

#### Generating the website

Generating the entire site is done with the `site` command, which accepts the following arguments and options.

```
Usage:
  site [options] [--] <db> [<out>]

Arguments:
  db                     Path to database.
  out                    Output directory. [default: "site"]

Options:
      --min              Minify output.
      --prev-db=PREV-DB  Previous database.
      --ext=EXT          File extension in URLs. [default: ".html"]
```

The only mandatory argument is a path to the database. The database is built by the [Steam importer][Steam importer] project, but you are not expected to have to run this yourself. Instead, latest database snapshots are available for download from the [snapshots tag][Snapshots].

⚠️ Note: If you do not use recent snapshots you may encounter errors generating the site.

It is recommended to specify the previous day's database with the `--prev-db` option. This is needed to calculate the movement indicators, otherwise this feature will be unavailable.

A complete command to generate the site may look like the following.

```
bin/generate site db/steam561.sqlite --prev-db db/steam560.sqlite
```

It will take a few minutes to generate the entire site because not only are there almost 150 pages but the ranking algorithm has to process the entire catalogue of games to calculate each ranking, too!

### Viewing the site

To view the generated website we need to run a local web server so absolute paths are resolved correctly. Fortunately, we can do this easily with the PHP built-in web server by running a command similar to the following.

```
php -S localhost:8113 -t '/path/to/Steam 250/site'
```

Then we can simply visit `localhost:8113` in our favourite web browser. Feel free to change the port to whatever you want and remember to set the path correctly to point to the same directory the site was generated in.

  [Build]: https://travis-ci.org/250/Steam-250
  [Build image]: https://travis-ci.org/250/Steam-250.svg?branch=master "Build status"
  [Style]: https://styleci.io/repos/110031821
  [Style image]: https://styleci.io/repos/110031821/shield?style=flat "Code style"
  [Snapshots]: https://github.com/250/Steam-250/releases/tag/snapshots

  [Steam 250]: https://steam250.com
  [Steam importer]: https://github.com/250/Steam-importer
  [Composer]: https://getcomposer.org
