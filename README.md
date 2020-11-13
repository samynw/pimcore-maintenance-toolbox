# pimcore-maintenance-toolbox
This provides a set of usefull tools for the Pimcore maintenance

Features include:
- a console command to get a list of maintenance tasks and whether they are locked or not
- a console command to manually unlock a maintenance task (for example after getting killed)

## Installation

```shell script
# Add package to composer dependencies
composer require samynw/pimcore-maintenance-toolbox

# Enable and install bundle
php bin/console pimcore:bundle:enable MaintenanceToolboxBundle
php bin/console pimcore:bundle:install MaintenanceToolboxBundle
```
## Configuration
The bundle can be configured through the extension manager in the Pimcore admin interface.

## Features

### Task overview

This command will print a table with all registered maintenance tasks and their locked status.
If possible also the lock expiration and current duration of the task is shown.

Options:
- `--locked`: only show the locked tasks
- `--sort=[name,lock]`: sort the table by name or by lock (default: name)

(note: sample outputs are shorted for readability)

```shell script
$ php bin/console maintenance:list

+----------------------------------+--------+---------------------+-----------+
| Maintenance task                 | Locked | Lock expiration     | Duration  |
+----------------------------------+--------+---------------------+-----------+
| archiveLogEntries                | ❌      |                     |           |
| asset_document_convert           | ❌      |                     |           |
| checkerrorlogsdb                 | ✔      | 2020-11-14 11:22:55 | 00h10m13s |
| cleanupBrickTables               | ❌      |                     |           |
| ...                              | ...    | ...                 | ...       |
| scheduledtasks                   | ❌      |                     |           |
| tmpstorecleanup                  | ❌      |                     |           |
| versioncleanup                   | ✔      | 2020-11-14 11:16:25 | 00h16m43s |
| versioncompress                  | ❌      |                     |           |
| VersionsCleanupStackTraceDb      | ❌      |                     |           |
+----------------------------------+--------+---------------------+-----------+
```

```shell script
$ php bin/console maintenance:list --locked

+------------------+--------+---------------------+-----------+
| Maintenance task | Locked | Lock expiration     | Duration  |
+------------------+--------+---------------------+-----------+
| checkerrorlogsdb | ✔      | 2020-11-14 11:22:55 | 00h12m20s |
| versioncleanup   | ✔      | 2020-11-14 11:16:25 | 00h18m50s |
+------------------+--------+---------------------+-----------+
```

```shell script
$ php bin/console maintenance:list --sort=lock

+----------------------------------+--------+---------------------+-----------+
| Maintenance task                 | Locked | Lock expiration     | Duration  |
+----------------------------------+--------+---------------------+-----------+
| versioncleanup                   | ✔      | 2020-11-14 11:16:25 | 00h20m01s |
| checkerrorlogsdb                 | ✔      | 2020-11-14 11:22:55 | 00h13m31s |
| archiveLogEntries                | ❌      |                     |           |
| asset_document_convert           | ❌      |                     |           |
| ...                              | ❌      |                     |           |
| versioncompress                  | ❌      |                     |           |
| VersionsCleanupStackTraceDb      | ❌      |                     |           |
+----------------------------------+--------+---------------------+-----------+
```

### Unlock maintenance tasks

The bundle includes a command to release the lock from a maintenance task.

However the locks are set to prevent concurring executions and therefor should not be removed manually.
In some cases however this might be usefull, for example if the maintenance process got killed
and the lock got stuck untill the expiration key ends.

To prevent reckless unlocking the following are made:
- feature is disabled by default (enable in module cofiguration)
- user first gets a warning and has to confirm the action

```shell script
$ php bin/console maintenance:release-lock versioncleanup

 !
 ! [CAUTION] THIS MIGHT BE AN UNSAFE OPERATION.
 !

 ! [NOTE] You've requested to remove the lock from a maintenance job.
 !        In normal circumstances this should never be done manually.
 !        Removing a job lock, might lead to concurring processes and unexpected behaviour.
 !        Do not continue unless you fully comprehend the possible consequences of this action.

 Are you sure you want to release the job lock for "versioncleanup"? (y/n)  (yes/no) [no]:
 > yes


 [OK] Job "versioncleanup" has been unlocked
```