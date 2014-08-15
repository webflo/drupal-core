Drupal /Core
============

This is a [Git subtree] (https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt) split of [Drupal] (https://github.com/drupal/drupal) 8's `core` directory.

How?
----

#### 1. Initialize the Drupal Git repository
Perform a split for the [8.0.x] (https://github.com/drupal/drupal/tree/8.0.x) branch:
``` bash
$ git clone https://github.com/drupal/drupal drupal-core --branch 8.0.x
$ cd drupal-core
```

Perform a split for a tag, for example the [8.0.0-alpha14] (https://github.com/drupal/drupal/tree/8.0.0-alpha14) tag:
``` bash
# Because we want to apply additional commits to a tag (see below), we cannot
# checkout the tag directly.
$ git checkout `git show-ref --hash 8.0.0-alpha14`
```

#### 2. Copy the Composer files to the `core` directory
This is - strictly speaking - not necessary, but required for the generated
repository to be pulled into other projects via Composer. This step needs to be
repeated whenever the Composer files change upstream.
``` bash
# Copy the composer.json file, replacing 'core/' with ''
$ sed 's/core\///' <composer.json >core/composer.json
$ cp composer.lock core/composer.lock
$ git add core/composer.json core/composer.lock
$ git commit -m "Copy the Composer files to the core directory."
```

#### 3. Update to the latest version of the remote repository
``` bash
$ git pull --rebase
```

#### 4. Create the split repository
``` bash
# `git subtree split` will return the commit ID of the generated subtree, so we
# store that in an environment variable for use below.
$ export SUBTREE_HASH=`git subtree split -P core`
```

#### 5. Publish the split repository
Publish the subtree of the 8.0.x branch:
```bash
$ git push git@github.com:tstoeckler/drupal-core.git $SUBTREE_HASH:8.0.x
```

Publish the subtree of the 8.0.0-alpha14 tag:
```bash
# Delete the tag so we can recreate it for the subtree.
$ git tag --delete 8.0.0-alpha14
$ git tag 8.0.0-alpha14 $SUBTREE_HASH
$ git push git@github.com:tstoeckler/drupal-core.git tag 8.0.0-alpha14
```
