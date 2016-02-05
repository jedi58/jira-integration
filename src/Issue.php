<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with Issue resources from the
 * Jira API
 */
class Issue extends JiraConnection
{
    /**
     * @var Authentication Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return Issue The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Creates a new ticket
     * @param string[] Settings to apply to the ticket
     * @return stdClass The object containing the issue key
     */
    public function create($data = array())
    {
        return $this->sendRequest('issue', $data, 'POST');
    }
    /**
     * Simple interface for {@link createTicket} but provides
     * less flexibility.
     * @param string $project The project to assign the new ticket to
     * @param string $title The title of the new ticket
     * @param string $description A description for the new ticket
     * @param string $issuetype The type of ticket being created
     * @param string[] $timetracking Time estimates for the ticket
     * @param string[] $custom Any custom properties to assign to the ticket
     * @return stdClass The key of the created ticket. e.g. DEMO-123
     */
    public function simpleCreate(
        $project,
        $title,
        $description,
        $issuetype = 'Bug',
        $timetracking = array(),
        $custom = array()
    ) {
        $data = array();
        $data['fields'] = array_merge(
            array(
                'project' => array(),
                'summary' => $title,
                'description' => $description,
                'issuetype' => array()
            ),
            $custom
        );
        if (is_numeric($project)) {
            $data['fields']['project']['id'] = $project;
        } else {
            $data['fields']['project']['key'] = $project;
        }
        if (is_numeric($issuetype)) {
            $data['fields']['issuetype']['id'] = $issuetype;
        } else {
            $data['fields']['issuetype']['name'] = $issuetype;
        }
        if (isset($data['timetracking']) && !empty($timetracking)) {
            $data['fields']['timetracking'] = $timetracking;
        }
        return $this->create($data);
    }
    /**
     * Updates the specified Jira ticket
     * @param string $issue_key The ticket to be update
     * @param string[] $data The changes to make to the ticket
     * @return stdClass Returns TRUE if successful
     */
    public function update($issue_key, $data)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key),
            $data,
            'PUT'
        );
    }
    /**
     * Deletes the specified ticket
     * @param string $issue_key The ticket to be removed
     * @param bool $remove_subtasks Flag indicating if sub-tasks can be removed
     * @return stdClass Returns TRUE if deletion was successful
     */
    public function delete($issue_key, $remove_subtasks = false)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(
                'deleteSubtasks' => (string) $remove_subtasks
            ),
            'DELETE'
        );
    }
    /**
     * Retrieves the specifed ticket
     * @param string $issue_key The ticket to be returned
     * @return StdClass The object containing the ticket
     */
    public function get($issue_key)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(),
            'GET'
        );
    }
    /**
     * Assigns the specified user to the ticket. A name should be passed here
     * otherwise it will assign the automatic assignee. Using null wil make
     * the ticket unassigned
     * @param string $issue_key The ticket to change assignee of
     * @param string $assignee The name of the user to assign the ticket to
     * @return stdClass The result of attempting to assign the user
     */
    public function assign($issue_key, $assignee = '-1')
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/assignee',
            array(
                'name' => $assignee
            ),
            'PUT'
        );
    }
    /**
     * Attaches a provided file to the speciifed issue
     * @param string $issue_key The identifier of the issue to attach the file to
     * @param string $filepath The system path of the file to upload
     * @return stdClass The result of uploading the file
     */
    public function attachFile($issue_key, $filepath)
    {
        return $this->sendRequest(
            'issue/' . $issue_key . '/attachments',
            array(
                'filename' => 'test',
                'file' => '@' . $filepath . ';filename=' . basename($filepath)
            ),
            'POST',
            true
        );
    }
    /**
     * Returns an array of issue config options available for a given project
     * @param string $proejctId The ID of the project to get issue config options for
     * @return stdClass The result of requesting config options
     */
    public function getProjectIssueAvailableConfig($projectId)
    {
        return $this->sendRequest(
            'issue/createmeta?projectKeys=' . urlencode($projectId) .
            '&issuetypeName=Bug&expand=projects.issuetypes.fields'
        );
    }
    /**
     * Returns the custom field specified by it's Id
     * @param string $field_id The Id of the custom field to return
     * @return stdClass The object containing the custom field
     */
    public function getCustomFieldOption($field_id)
    {
        return $this->sendRequest('customFieldOption/' . urlencode($field_id));
    }
    /**
     * Updates the metadata for the specified issue
     * @param string $issue_key The identifier for the issue to be modified
     * @param string[] The array of changes to make to the issues metadata
     * @return stdClass The result of attempting to edit the metadata
     */
    public function editMetadata($issue_key, $data)
    {
        if (!isset($data['fields'])) {
            $data = array(
                'fields' => $data
            );
        }
        return $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/editmeta',
            $data,
            'GET'
        );
    }
}
