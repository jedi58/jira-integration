CHANGELOG
=========

1.4.0
-----

 * `app/console`: Added `--hash` switch which accepts a base-64 encoded JSON object
   that has been constructed from a bookmarklet or other. This is
   then used for default values.

1.3.0
-----

 * Added support for `customfield` configuration in YAML for console application prompts
 * Added `install.sh` to speed up setting up of console application for use

1.2.0
-----

 * Added console application to allow for interactive access to the Jira API with
   commands for `issue:create`, `issue:get`, `comment:get`
 * Added support for a YAML configuration file
 
1.1.0
-----

 * Improved code quality and changed `create*`, `update*`, `delete*`, `getAll*` function names to be shorter
 * Split `Worklog` object from `Issue` object
 * Moved result handling to `JiraConnection::sendRequest` so all functions can take advantage of exceptions
 
1.0.1
-----
 * Added support for worklog updates under `Issue` object [#1]
 * Added support for attachments [#2]
 
1.0.0
 * Initial release of JiraIntegration component.
