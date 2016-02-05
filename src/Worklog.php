<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with the worklog part of the Issue
 * resource from the Jira API
 */
class Worklog extends JiraConnection {
	/**
	 * @var Authentication Reference to instance of self
	 */
	private static $instance;
	/**
	 * Returns a singleton instance of this class
	 * @return Worklog The singleton instance
	 */
	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}
    /**
     * 
     * @param string $issue_key The issue to add the worklog update to
     * @param string|int $timeSpent The amount of time spent on the current issue
     * @param string[] $options Any additional options that should be applied
     * @return stdClass The result of adding the worklog entry
     */
    public function create($issue_key, $timeSpent = '', $options = array())
    {
        $timeKey = is_int($timeSpent) ? 'timeSpentSeconds' : 'timeSpent';
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/worklog',
            array_merge(
                array(
                    $timeKey => $timeSpent
                ),
                $options
            ),
            'POST'
        );
    }
    /**
     * Updates an existing worklog entry
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @param string|int $timeSpent The amount of time spent on the current issue
     * @param string[] $options Any additional options that should be applied
     * @return stdClass The result of updating the worklog entry
     */
    public function update(
            $issue_key,
            $worklog_key,
            $timeSpent = '',
            $options = array()
    ) {
        $timeKey = is_int($timeSpent) ? 'timeSpentSeconds' : 'timeSpent';
        return $this->sendRequest(
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
    }
    /**
     * Deletes a specific worklog entry
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @return stdClass The result of updating the worklog entry
     */
    public function delete($issue_key, $worklog_key)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/worklog/' . urlencode($worklog_key),
            array(),
            'DELETE'
        );
    }
    /**
     * Returns a specific worklog entry for a given Issue
     * @param string $issue_key The issue to add the worklog update to
     * @param string $worklog_key The ID of the worklog entry to update
     * @return stdClass The result of updating the worklog entry
     */
    public function get($issue_key, $worklog_key)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/worklog/' . urlencode($worklog_key)
        );
    }
    /**
     * Returns all worklog entries for a given issue
     * @param string $issue_key The issue to add the worklog update to
     * @return stdClass The result of updating the worklog entry
     */
    public function getAll($issue_key)
    {
        return $this->sendRequest('issue/' . urlencode($issue_key) . 
                '/worklog/');
    }
}
