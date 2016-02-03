<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

class Issue extends JiraConnection {
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
     * @throws \Exception
     * @return string[] The key of the created ticked. e.g. DEMO-123
     */
    public function createTicket($data = array())
    {
        $result = $this->sendRequest('issue', $data, 'POST');
        if ($this->getLastResponseCode() !== 201) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to create Jira ticket');
            } else {
                return array('key' => null);
            }
        }
        return array('key' => $result->key);
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
     * @return string[] The key of the created ticket. e.g. DEMO-123
     */
    public function simpleCreateTicket(
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
        return $this->createTicket($data);
    }
    /**
     * Updates the specified Jira ticket
     * @param string $issue_key The ticket to be update
     * @param string[] $data The changes to make to the ticket
     * @throws \Exception
     * @return bool Returns TRUE if successful
     */
    public function updateTicket($issue_key, $data)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            $data,
            'PUT'
        );
        if ($this->getLastResponseCode() !== 204) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to update Jira ticket');
            } else {
                return false;
            }
        }
        return true;
    }
    /**
     * Deletes the specified ticket
     * @param string $issue_key The ticket to be removed
     * @param bool $remove_subtasks Flag indicating if sub-tasks can be removed
     * @throws \Exception
     * @return bool Returns TRUE if deletion was successful
     */
    public function deleteTicket($issue_key, $remove_subtasks = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(
                'deleteSubtasks' => (string) $remove_subtasks 
            ),
            'DELETE'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 401:
                    throw new \Exception('Error deleting ticket: User not authenticated');
                    break;

                case 403:
                    throw new \Exception('Error deleting ticket: Permission denied');
                    break;

                case 404:
                    throw new \Exception('Error deleting ticket: Does not exist');
                    break;

                case 400:
                default:
                    throw new \Exception('Error deleting ticket');
            }
        }
        return false;
    }
    /**
     * Retrieves the specifed ticket
     * @param string $issue_key The ticket to be returned
     * @throws \Exception
     * @return StdClass The object containing the ticket
     */
    public function getTicket($issue_key)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(),
            'GET'
        );
        if ($this->getLastResponseCode() !== 204
                && $this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket could not be returned');
        }
        return $result;
    }
    /**
     * Assigns the specified user to the ticket. A name should be passed here
     * otherwise it will assign the automatic assignee. Using null wil make
     * the ticket unassigned
     * @param string $issue_key The ticket to change assignee of
     * @param string $assignee The name of the user to assign the ticket to
     * @throws \Exception
     * @return bool The result of attempting to assign the user
     */
    public function assignTicket($issue_key, $assignee = '-1')
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/assignee',
            array(
                'name' => $assignee
            ),
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 401:
                    throw new \Exception('Error assigning user: Permission denied');
                    break;

                case 404:
                    throw new \Exception('Error assigning user: Issue or user not found');
                    break;

                case 400:
                default:
                    throw new \Exception('Error assigning user to ticket');
            }
        }
        return false;
    }
    /**
     * Returns an array of issue config options available for a given project
     * @param string $proejctId The ID of the project to get issue config options for
     * @return SimpleXML The result of requesting config options
     */
    public function getProjectIssueAvailableConfig($projectId)
    {
        return $this->sendRequest(
            'issue/createmeta?projectKeys=' . urlencode($projectId) .
            '&issuetypeName=Bug&expand=projects.issuetypes.fields'
        );
    }
    /**
     *
     */
    public function getCustomFieldOption($field_id)
    {
        return $this->sendRequest('customFieldOption/' . urlencode($field_id));
    }
    /**
     * Updates the metadata for the specified issue
     * @param string $issue_key The identifier for the issue to be modified
     * @param string[] The array of changes to make to the issues metadata
     * @throws \Exception
     * @return SimpleXML The result of attempting to edit the metadata
     */
    public function editMetadata($issue_key, $data)
    {
        if (!isset($data['fields'])) {
            $data = array(
                'fields' => $data
            );
        }
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/editmeta',
            $data,
            'GET'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Issue metadata not found');
        }
        return array();
    }
    /**
     * 
     * @param string $issue_key The issue to add the worklog update to
     * @param string|int $timeSpent The amount of time spent on the current issue
     * @param string[] $options Any additional options that should be applied
     * @return SimpleXML The result of adding the worklog entry
     * @throws \Exception
     */
    public function addWorklog($issue_key, $timeSpent = '', $options = array())
    {
        $timeKey = is_int($timeSpent) ? 'timeSpentSeconds' : 'timeSpent';
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/worklog',
            array_merge(
                array(
                    $timeKey => $timeSpent
                ),
                $options
            ),
            'POST'
        );
        $response = $this->getLastResponseCode();
        if ($response === 201) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Worklog addition invalid');
                    break;
                case 403:
                    throw new \Exception('Permission denied adding worklog');
                    break;
            }
        }
        return array();
    }
    /**
     * Updates an existing worklog entry
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @param string|int $timeSpent The amount of time spent on the current issue
     * @param string[] $options Any additional options that should be applied
     * @return SimpleXML The result of updating the worklog entry
     * @throws \Exception
     */
    public function updateWorklog(
            $issue_key,
            $worklog_key,
            $timeSpent = '',
            $options = array()
    ) {
        $timeKey = is_int($timeSpent) ? 'timeSpentSeconds' : 'timeSpent';
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/worklog/' . urlencode($worklog_key),
            array_merge(
                array(
                    $timeKey => $timeSpent
                ),
                $options
            ),
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Worklog update invalid');
                    break;
                case 403:
                    throw new \Exception('Permission denied updating worklog');
                    break;
            }
        }
        return array();
    }
    /**
     * Deletes a specific worklog entry
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @return SimpleXML The result of updating the worklog entry
     * @throws \Exception
     */
    public function deleteWorklog($issue_key, $worklog_key)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/worklog/' . urlencode($worklog_key),
            array(),
            'DELETE'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Worklog deletion invalid');
                    break;
                case 403:
                    throw new \Exception('Permission denied deleting worklog');
                    break;
            }
        }
        return array();
    }
    /**
     * Returns a specific worklog entry for a given Issue
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @return SimpleXML The result of updating the worklog entry
     * @throws \Exception
     */
    public function getWorklog($issue_key, $worklog_key)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/worklog/' . urlencode($worklog_key)
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Worklog entry not found for issue ' . 
                    $issue_key);
        }
        return array();
    }
    /**
     * Returns all worklog entries for a given issue
     * @param string $issue_key The issue to add the worklog update to
     * @return SimpleXML The result of updating the worklog entry
     * @throws \Exception
     */
    public function getAllWorklog($issue_key)
    {
        $result = $this->sendRequest('issue/' . urlencode($issue_key) . 
                '/worklog/');
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Worklog not found for issue ' . $issue_key);
        }
        return array();
    }
    /**
     * Attaches a provided file to the speciifed issue
     * @param string $issue_key The identifier of the issue to attach the file to
     * @param string $filepath The system path of the file to upload
     * @return SimpleXML The result of uploading the file
     */
    public function attachFile($issue_key, $filepath)
    {
        $result = $this->sendRequest(
            'issue/' . $issue_key . '/attachments',
            array(
                'filename' => 'test',
                'file' => '@' . $filepath . ';filename=' . basename($filepath)
            ),
            'POST',
            true
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Upload failed');
        }
        return array();
    }
}
