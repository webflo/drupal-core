Drupal /Core
============

This is a git subtree split of Drupal 8's `core` directory.

How?
----

This was done using the [git-subsplit](https://github.com/dflydev/git-subsplit)
script by [Simensen](https://github.com/simensen)...

#### 1. Initialize the subsplit in a git repository:

``` bash
$ git subsplit init https://github.com/drupal/drupal
```

#### 2. Copy the Composer files to the `core` directory
This is optional, but required for the generated repository to be pulled into other projects via Composer. This step needs to be repeated whenever the Composer files change upstream.
``` bash
$ cd .subsplit
$ git checkout 8.0.x
# Copy the composer.json file, replacing 'core/' with ''
$ sed 's/core\///' <composer.json >core/composer.json
$ cp composer.lock core/composer.lock
$ git add core/composer.json core/composer.lock
$ git commit -m "Copy the Composer files to the core directory."
```

#### 3. Update to the latests version of the remote repository:

``` bash
$ cd .subsplit
$ git fetch origin
$ cd ..
```
(`git subsplit update` currently does not work because the Drupal repository does not contain a master branch. See https://github.com/dflydev/git-subsplit/issues/13)

#### 4. Publish
Publish the `8.0.x` branch:
```bash
$ git subsplit publish "core:git@github.com:tstoeckler/drupal-core.git" --heads="8.0.x" --no-tags
```

Publish the last two alpha releases:
```bash
$ git subsplit publish "core:git@github.com:tstoeckler/drupal-core.git" --tags="8.0-alpha13 8.0.0-alpha14" --no-heads
```

