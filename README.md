# JiraIntegration
A PHP class for interacting with the Atlassian Jira API, issue and project tracking software used by Agile teams.

The idea behind this class is that you can use this to pull out information to be used on your own website(s), or have your own web applications interact with it.

## Usage

1. [Creating a ticket (Simple)](#createSimple)
2. [Creating a ticket](#create)
3. [Updating a ticket (Simple)](#updateSimple)
4. [Updating a ticket](#update)
5. [Adding comments to tickets](#addComment)
6. [Retrieving a specific ticket](#getTicket)
7. [Retrieving a list of all projects](#getAllProjects)
8. [Retrieving a list of all issue types](#getAllIssueTypes)
9. [Retrieving available config options for a project](#getProjectIssueAvailableConfig)
10. [Retrieving a custom field](#getCustomFieldOption)
11. [Retrieve a list of assignable users](#getAssignableUsers)

<a name="createSimple"></a>
### Creating a ticket (Simple)
Creates a new ticket and assigns it to the specified project (in the below example this is `TES`).
```php
$jira = new JiraIntegration('https://example.atlassian.net');
$jira->authenticate('user', 'password');
$jira->simpleCreateTicket('TES', 'A test ticket', 'There is an issue here - please fix it');
```

This can also take optional parameters for setting the type of ticket, time tracking, and any other additional options in an array.
