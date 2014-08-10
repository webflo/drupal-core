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

#### 2. Update to the latests version of the remote repository:

``` bash
$ cd .subsplit
$ git fetch origin
$ cd ..
```
(`git subsplit update` currently does not work because the Drupal repository does not contain a master branch. See https://github.com/dflydev/git-subsplit/issues/13)

#### 3. Publish
Publish the `8.0.x` branch:
```bash
$ git subsplit publish "core:git@github.com:tstoeckler/drupal-core.git" --heads="8.0.x" --no-tags
```

Publish the last two alpha releases:
```bash
$ git subsplit publish "core:git@github.com:tstoeckler/drupal-core.git" --tags="8.0-alpha13 8.0.0-alpha14" --no-heads
```

