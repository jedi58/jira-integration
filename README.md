# JiraIntegration
A PHP class for interacting with the Atlassian Jira API, issue and project tracking software used by Agile teams.

The idea behind this class is that you can use this to pull out information to be used on your own website(s), or have your own web applications interact with it.

## Usage

1. [Creating a ticket (Simple)](#createSimple)
2. [Creating a ticket](#create)
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
Creates a new ticket and assigns it to the specified project (in the below example this is `DEMO`).
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');
$case_id = $jira->simpleCreateTicket('DEMO', 'A test ticket', 'There is an issue here - please fix it');
```

This can also take optional parameters for setting the type of ticket, time tracking, and any other additional options in an array.


<a name="create"></a>
### Creating a ticket
This is a more versatile version of the function for creating a ticket in Jira. It will only take an array as it's parameter and expects you to pass in everything required.
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');
$case_id = $jira->createTicket(array('fields' => array(
    'project' => array(
        'key' => 'DEMO'
    ),
    'summary' => 'A test ticket',
    'description' => 'There is an issue here - please fix it',
    'issuetype' => array(
        'name' => 'Bug'
    )
)));
```

As with the `simpleCreateTicket` function this will return the ID of the ticket that has been created.


<a name="update"></a>
###Updating a ticket
This is a more versatile way of updating a ticket's properties - all changes must be passed in to the array.
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');
$case_id = $jira->createTicket(array('fields' => array(
    'description' => 'This is the new description of the ticket'
)));
```


<a name="addComment"></a>
###Adding comments to tickets
This will add a comment to the ticket.
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');
$jira->addComment('This is a comment!');
```


<a name="getTicket"></a>
###Retrieving a specific ticket
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```


<a name="getAllProjects"></a>
###Retrieving a list of all projects
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```


<a name="getAllIssueTypes"></a>
###Retrieving a list of all issue types
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```


<a name="getProjectIssueAvailableConfig"></a>
###Retrieving available config options for a project
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```


<a name="getCustomFieldOption"></a>
###Retrieving a custom field
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```


<a name="getAssignableUsers"></a>
###Retrieve a list of assignable users
```php
$jira = new JiraIntegration('https://jira.atlassian.com');
$jira->authenticate('user', 'password');

```
