Drupal /Core
============

This is a git subtree split of Drupal 8's /core directory.

How?
----

This was done using the [git-subsplit](https://github.com/dflydev/git-subsplit)
script by [Simensen](https://github.com/simensen)...

1. Initialize the subsplit in a git repository:

``` bash
$ git subsplit init https://github.com/drupal/drupal
```

2. Update to the latests version of the remote repository:

``` bash
$ git subsplit update
```

3. Publish

```bash
$ git subsplit publish "
    core:git@github.com:robloach/core.git
  " --heads=master
```
