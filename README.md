Drupal `core`
============

This is a [Git subtree] (https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt) split of [Drupal] (https://github.com/drupal/drupal) 8's `core` directory which can be used to build the directory structure for a Drupal site and has the following advantages over pulling in the entire upstream Drupal repository:
- All the components of the Drupal site including Drupal core as well as contributed modules and themes can be pulled in via [Composer] (https://getcomposer.org)
- One has full control over `index.php`, `.htaccess`, `robots.txt`, etc. as those files will not be overridden by a Drupal core update

Usage
---
Add the following to your site's `composer.json`:
``` json
{
  "require": {
    "composer/installers": "dev-drupal-core",
    "drupal/drupal-core": "8.0.*"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/tstoeckler/installers"
    },
    {
      "type": "package",
      "package": {
        "name": "drupal/drupal-core",
        "description": "Drupal is an open source content management platform powering millions of websites and applications.",
        "type": "drupal-core",
        "version": "8.0.0-dev",
        "dist": {
          "url": "https://github.com/tstoeckler/drupal-core/archive/8.0.x.zip",
          "type": "zip"
        },
        "source": {
          "url": "git@github.com:tstoeckler/drupal-core.git",
          "type": "git",
          "reference": "8.0.x"
        },
        "license": "GPL-2.0+",
        "require": {
          "php": ">=5.4.2",
          "sdboyer/gliph": "0.1.*",
          "symfony/class-loader": "2.5.*",
          "symfony/css-selector": "2.5.*",
          "symfony/dependency-injection": "2.5.*",
          "symfony/event-dispatcher": "2.5.*",
          "symfony/http-foundation": "2.5.*",
          "symfony/http-kernel": "2.5.*",
          "symfony/routing": "2.5.*",
          "symfony/serializer": "2.5.*",
          "symfony/validator": "2.5.*",
          "symfony/yaml": "dev-master#499f7d7aa96747ad97940089bd7a1fb24ad8182a",
          "twig/twig": "1.16.*",
          "doctrine/common": "dev-master#a45d110f71c323e29f41eb0696fa230e3fa1b1b5",
          "doctrine/annotations": "1.2.*",
          "guzzlehttp/guzzle": "~5.0",
          "symfony-cmf/routing": "1.3.*",
          "easyrdf/easyrdf": "0.8.*",
          "phpunit/phpunit": "4.1.*",
          "phpunit/phpunit-mock-objects": "dev-master#e60bb929c50ae4237aaf680a4f6773f4ee17f0a2",
          "zendframework/zend-feed": "2.2.*",
          "mikey179/vfsStream": "1.*",
          "stack/builder": "1.0.*",
          "egulias/email-validator": "1.2.*"
        },
        "autoload": {
          "psr-4": {
            "Drupal\\Core\\": "lib/Drupal/Core",
            "Drupal\\Component\\": "lib/Drupal/Component",
            "Drupal\\Driver\\": "../drivers/lib/Drupal/Driver"
          },
          "files": [
            "lib/Drupal.php"
          ]
        }
      }
    }
  ],
  "extra": {
    "installer-paths": {
      "core": ["type:drupal-core"]
    }
  }
}
```

This will download Drupal's `core` directory into the root of the repository. If you want your Drupal installation to be in a subdirectory of the repository simply replace `"core"` in the `"installer-paths"` section above with `"web/core"` or similar. Of course any other version declaration supported by Composer works as well, so you can also target a specific tag or commit of this repository.

Many Drupal 8 modules ship with a `composer.json` file so you can add the following to your `composer.json` so that you can pull in all modules which support this via Composer as well, and they will be placed into the `modules` directory where Drupal expects them to be:
``` json
{
  "extra": {
    "installer-paths": {
      "modules": ["type:drupal-module"]
    }
  }
}
```
This works similarly for themes:
``` json
{
  "extra": {
    "installer-paths": {
      "themes": ["type:drupal-theme"]
    }
  }
}
```

In order to actually install Drupal you need to copy some files from the upstream Drupal repository into your repository root. The `index.php`, `sites/default/default.services.yml` and `sites/default/default.settings.php` files are required to operate Drupal and the `.htaccess` or `web.config` and the `robots.txt` files are recommended to copy as well. The `composer.json` file needs to be copied as well for [Drush] (https://github.com/drush-ops/drush) to work with your Drupal installation. Copy the rest of the files to your liking.

You can copy the files by cloning the upstream Drupal repository and copying them manually or by fetching them directly via the command line. For example:
``` bash
# Copy index.php from the upstream Drupal repository
wget https://raw.githubusercontent.com/drupal/drupal/8.0.x/index.php
```

#### Versions
Because Drupal's `composer.json` is in the repository root, not in the `core` directory, most of Drupal's `composer.json` needs to be duplicated, unfortunately. For the same reason pulling in a different version of Drupal is more tedious than it should be. To use the [8.0.0-beta2] (https://github.com/tstoeckler/drupal-core/releases/tag/8.0.0-beta2) version of Drupal, for example, adapt the respective parts of your `composer.json` to look like the following:
``` json
{
  "require": {
    "drupal/drupal-core": "8.0.0-beta2"
  },
  "repositories": [
    {
      "package": {
        "version": "8.0.0-beta2",
        "dist": {
          "url": "https://github.com/tstoeckler/drupal-core/archive/8.0.0-beta2.zip",
        },
        "source": {
          "reference": "tags/8.0.0-beta2"
        }
      }
    }
  ]
}
```

How this repository is maintained
----

#### Initialize the repository
``` bash
# Clone the repository
git clone https://github.com/tstoeckler/drupal-core.git
cd drupal-core

# Initialize the upstream repository
./subtree-split init
```

#### Update the respository
``` bash
./subtree-split update
```

#### Push the split repository
```bash
# Push the 8.0.x branch
./subtree-push branch 8.0.x

# Push the 8.0.0-beta2 tag
./subtree-push tag 8.0.0-beta2
```

#### Configuration
To use different upstream and downstream repositories or a different directory,
place a `subtree-split.config` file in the root of this repository, that looks
like the following:
```bash
UPSTREAM_REPOSITORY=https://github.com/drupal/drupal
UPSTREAM_DIRECTORY=upstream
DOWNSTREAM_REPOSITORY=git@github.com:tstoeckler/drupal-core.git
```
By default the `DOWNSTREAM_REPOSITORY` variable is taken from the `remote.origin.url` Git configuration value so that it does not need to be changed when cloning this repository.
