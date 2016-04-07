# JiraIntegration

[![Build Status](https://travis-ci.org/jedi58/JiraIntegration.svg?branch=master)](https://travis-ci.org/jedi58/JiraIntegration)
[![StyleCI](https://styleci.io/repos/50450886/shield)](https://styleci.io/repos/50450886)
[![Code Climate](https://codeclimate.com/github/jedi58/JiraIntegration/badges/gpa.svg)](https://codeclimate.com/github/jedi58/JiraIntegration)
[![Test Coverage](https://codeclimate.com/github/jedi58/JiraIntegration/badges/coverage.svg)](https://codeclimate.com/github/jedi58/JiraIntegration/coverage)

A PHP component for interacting with the Atlassian Jira API, issue and project tracking software used by Agile teams.

The idea behind this class is that you can use this to pull out information to be used on your own website(s), or have your own web applications interact with it. The Jira API documentation this is developed against is: https://docs.atlassian.com/jira/REST/latest/

## Usage

1. [Providing authentication details for the Jira API](#authentication)
2. [Prompting for Custom Fields](#customFields)
3. [Creating a ticket (Simple)](#createSimple)
4. [Creating a ticket](#create)
5. [Updating a ticket](#update)
6. [Adding comments to tickets](#addComment)
7. [Retrieving a specific ticket](#getTicket)
8. [Retrieving a list of all projects](#getAllProjects)
9. [Retrieving a list of all issue types](#getAllIssueTypes)
10. [Retrieving available config options for a project](#getProjectIssueAvailableConfig)
11. [Retrieving a custom field](#getCustomFieldOption)
12. [Retrieve a list of assignable users](#getAssignableUsers)


<a name="authentication"></a>
### Providing authentication details for the Jira API
All Jira API functions require themselves to be authenticated. Your application must first use the `Authentication` object to provide these details.
```php
$auth = Authentication::getInstance(
    'https://jira.atlassian.com'
    'user','password'
);
```
These details will then be automatically used when utilising any of the below objects.


<a name="customFields"></a>
### Prompting for Custom Fields
When using the console application you can prompt for customfield question from Jira by updating the `jira.yml` configuraiton file to include the customfield ID as specified by Jira, the type of question (ChoiceQuestion or Question), and any help hint text (optional).
```yaml
custom:
  customfield_12345
    type: ChoiceQuestion
    help: This is a question that will display allowed values to chose from
  customfield_67890
    type: Question
```

The above example shows both types of quesitons and how they can be configured.


<a name="createSimple"></a>
### Creating a ticket (Simple)
Creates a new ticket and assigns it to the specified project (in the below example this is `DEMO`).
```php
$case = Issue::getInstance()->simpleCreate(
    'DEMO',
    'A test ticket',
    'There is an issue here - please fix it',
    'Bug',
    array(
        'originalEstimate' => '1d 2h 25m'
        'remainingEstimate' => ''
    )
);
```

This can also take optional parameters for setting the type of ticket, time tracking, and any other additional options in an array.


<a name="create"></a>
### Creating a ticket
This is a more versatile version of the function for creating a ticket in Jira. It will only take an array as it's parameter and expects you to pass in everything required.
```php
$case_id = Issue::getInstance()->create(array('fields' => array(
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

As with the `simpleCreate` function this will return an stClass containing the ID of the ticket that has been created.


<a name="update"></a>
### Updating a ticket
This is a more versatile way of updating a ticket's properties - all changes must be passed in to the array.
```php
$case = Issue::getInstance()->update('DEMO-1234', array('fields' => array(
    'summary' => 'This is the new description of the ticket'
)));
```


<a name="deleteTicket"></a>
### Deleting a ticket
If a ticket has sub-tasks then it will be necessary to pass `true` into this function also in order to confirm that they should be removed.
```php
$case = Issue::getInstance()->delete('DEMO-1234');
```


<a name="getTicket"></a>
### Retrieving a specific ticket
This will return an `StdClass` object containing the ticket and all it's properties.
```php
$ticket = Issue::getInstance()->get('DEMO-123');
```


<a name="addComment"></a>
###Adding comments to tickets
This will add a comment to the ticket.
```php
Comment::getInstance()->create('DEMO-123', 'This is a comment!', array(
    'type' => 'role',
    'value' => 'Administrators'
));
```

The result returned is an array with the only element being the timestamp the comment was added. The array in this example represents the visibility of the comment and is optional.


<a name="getAllProjects"></a>
###Retrieving a list of all projects
```php
$projects = Project::getInstance()->getAll();
```


<a name="getAllIssueTypes"></a>
###Retrieving a list of all issue types
```php
$issue_types = Issue::getInstance()->getIssueTypes();
```


<a name="getProjectIssueAvailableConfig"></a>
###Retrieving available config options for a project
```php
$available_config = Issue::getInstance()->getProjectIssueAvailableConfig('SUP');
```


<a name="getCustomFieldOption"></a>
###Retrieving a custom field
```php
$custom = Issue::getInstance()->getCustomFieldOption(1);
```


<a name="getAssignableUsers"></a>
###Retrieve a list of assignable users
```php
$users = User::getInstance()->getAll();
```
