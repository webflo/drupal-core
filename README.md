# Drupal `core`

This is a [Git subtree] (https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt) split of [Drupal] (https://github.com/drupal/drupal) 8's `core` directory which can be used to build the directory structure for a Drupal site and has the following advantages over pulling in the entire upstream Drupal repository:
- All the components of the Drupal site including Drupal, contributed modules and themes, as well as external libraries can be pulled in via [Composer] (https://getcomposer.org)
- Drupal and any external libraries can be bootstrapped via Composer (i.e. without installing any modules for the external libraries)
- One has full control over `index.php`, `.htaccess`, `robots.txt`, etc. as those files will not be overridden by a Drupal core update

## Usage

*Note: If you are using a legacy version of this repository, please comment in [this issue] (https://github.com/tstoeckler/drupal-core/issues/6)!*

Add the following to your site's `composer.json`:
``` json
{
    "require": {
        "composer/installers": "^1.0.20",
        "drupal/drupal-core": "8.0.*"
    },
    "repositories": [
        {
            "type": "vcs",
             "url": "https://github.com/composer/installers"
        },
        {
            "type": "vcs",
            "url": "https://github.com/tstoeckler/drupal-core"
        }
    ]
}
```
### Directory layout
This will download Drupal's `core` directory into the root of the repository. If a Drupal 8 modules ships with a `composer.json` file, it will be downloaded into the `modules` directory where Drupal expects it. This works analogously for themes or installation profiles.

If you want to place Drupal core or modules, themes or installation profiles into different directories you can provide an `"installer-paths"` configuration directive in the `"extra"` section of the `composer.json`. To place the entire Drupal site in a subdirectory of the repository named `web`, for example, use the following:
``` json
{
    "extra": {
        "installer-paths": {
            "web/core": ["type:drupal-core"],
            "web/modules": ["type:drupal-module"],
            "web/profiles": ["type:drupal-profile"],
            "web/themes": ["type:drupal-theme"]
        }
    }
}
```
Or to place modules into a `contrib` subdirectory within the `modules` directory use:
``` json
{
    "extra": {
        "installer-paths": {
            "modules/contrib": ["type:drupal-module"]
        }
    }
}
```

### Site template
In order to actually install Drupal you need to copy some files from the upstream Drupal repository into your repository root. The `index.php`, `sites/default/default.services.yml` and `sites/default/default.settings.php` files are required to operate Drupal and the `.htaccess` or `web.config` and the `robots.txt` files are recommended to copy as well. Copy the rest of the files to your liking.

You can copy the files by cloning the upstream Drupal repository and copying them manually or by fetching them directly via the command line. For example:
``` bash
# Copy index.php from the upstream Drupal repository
wget https://raw.githubusercontent.com/drupal/drupal/8.0.x/index.php
```

See my [Drupal Site Template] (https://github.com/tstoeckler/drupal-site-template) for an example directory layout with the files needed to install and run Drupal including a `composer.json` file as given above. The template also includes the `index.php` modification and the `drushrc.php` in the way detailed below. Note that this site template follows the best practice of having the Drupal root be in a subdirectory of the repository, which is called `web` in this particular case.

### Drupal's autoloader
The upstream Drupal repository contains all Composer dependencies and the Composer autoloader in the `core/vendor` directory. One of the purposes of this repository, however, is to take control of the autoloader used for bootstrapping Drupal. If you have pulled in additional libraries in your `composer.json` you will not be able to autoload them using Drupal's hardcoded autoloader. To instead use the autoloader generated from your `composer.json` for Drupal requests simply change the following line in `index.php`:
```php
$autoloader = require_once __DIR__ . '/core/vendor/autoload.php';
```
appropriately, for example into:
```php
$autoloader = require_once __DIR__ . '/vendor/autoload.php';
```
Adapting `install.php` (or `authorize.php` or `rebuild.php`) works in the same way, although those are in located in the `core` directory [unfortunately] (https://www.drupal.org/node/2406681) and thus modifications to these files cannot be placed under version control.

### Drush's Drupal autoloader
When bootstrapping a Drupal site [Drush] (https://github.com/drush-ops/drush) loads Drupal's autoloader and by default uses Drupal's shipped autoloader (`core/vendor/autoload.php`). This, too, defies the purpose of this repository and will lead to fatal errors in case you are using a local Drush version installed from the same `composer.json` as Drupal. To make Drush use the autoloader generated from your `composer.json` call `drush_drupal_load_autoloader()` with the `$new_autoloader` argument from the site's `drushrc.php`. For example:
```php
// In drush/drushrc.php
$autoloader = require __DIR__ . '/../../vendor/autoload.php';
drush_drupal_load_autoloader(NULL, $autoloader);
```

## About this repository
This repository contains the Drupal `core` subtree split explained above in the `8.0.x` branch mirroring the upstream branch. The `master` branch contains this `README.md`, an empty `composer.json` which is required for this repository to work with [Packagist] (https://packagist.org) and the `subtree-split` bash script which is used for maintaining the subtree split (and a `.gitignore` file). The usage of this script is explained in detail below. The tags in this repository are subtree splits of the respective upstream tags.

### Usage of `./subtree-split`

#### Initializing the repository
``` bash
# Clone the repository
git clone https://github.com/tstoeckler/drupal-core.git
cd drupal-core

# Initialize the upstream repository
./subtree-split init
```

#### Updating the respository
``` bash
./subtree-split fetch
```

#### Publishing the split repository
```bash
# Publish the 8.0.x branch
./subtree-split push branch 8.0.x

# Publish the 8.0.0-beta2 tag
./subtree-split push tag 8.0.0-beta2
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
By default the `DOWNSTREAM_REPOSITORY` variable is taken from the `remote.origin.url` Git configuration value so that it does not need to be changed when forking this repository.
