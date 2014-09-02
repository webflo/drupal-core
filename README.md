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
      "type": "vcs",
      "url": "https://github.com/tstoeckler/drupal-core"
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

Many Drupal 8 modules ship with a `composer.json` file so you can add a 
``` json
"modules": ["type:drupal-module"]
```
line to the `"installer-paths"` section so that you can pull in all modules which support this via Composer as well, and they will be placed into the `modules` directory where Drupal expects them to be. This works similarly for themes.

In order to actually install Drupal you need to copy some files from the upstream Drupal repository into your repository root. The `index.php`, `sites/default/default.services.yml` and `sites/default/default.settings.php` files are required to operate Drupal and the `.htaccess` or `web.config` and the `robots.txt` files are recommended to copy as well. The `composer.json` file needs to be copied as well for [Drush] (https://github.com/drush-ops/drush) to work with your Drupal installation. Copy the rest of the files to your liking.

You can copy the files by cloning the upstream Drupal repository and copying them manually or by fetching them directly via the command line. For example:
``` bash
# Copy index.php from the upstream Drupal repository
wget https://raw.githubusercontent.com/drupal/drupal/8.0.x/index.php
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

# Push the 8.0.0-alpha 14 tag
./subtree-push tag 8.0.0-alpha14
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

