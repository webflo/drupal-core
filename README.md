Drupal /Core
============

This is a [Git subtree] (https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt) split of [Drupal] (https://github.com/drupal/drupal) 8's `core` directory.

How?
----

#### 1. Initialize the Drupal Git repository

``` bash
$ git clone https://github.com/drupal/drupal drupal-core --branch 8.0.x
$ cd drupal-core
```

#### 2. Copy the Composer files to the `core` directory
This is optional, but required for the generated repository to be pulled into other projects via Composer. This step needs to be repeated whenever the Composer files change upstream.
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
# git subtree split will return the commit ID of the generated subtree, so we
# store that in an environment variable for use below.
$ export SUBTREE_COMMIT_ID=`git subtree split -P core`
```

#### 5. Publish the split repository
```bash
$ git push git@github.com:tstoeckler/drupal-core.git $SUBTREE_COMMIT_ID:8.0.x
```

