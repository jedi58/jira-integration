<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with the priority part of the Issue
 * resource from the Jira API
 */
class Priority extends JiraConnection
{
    /**
     * @var Priority Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return Priority The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Returns a specific priority
     * @param string $priorityId The issue to add the worklog update to
     * @return stdClass The priority requested
     */
    public function get($priorityId)
    {
        return $this->sendRequest('priority/' . urlencode($priorityId));
    }
    /**
     * Returns all priorities
     * @return stdClass The array of all priority objects returned from Jira
     */
    public function getAll()
    {
        return $this->sendRequest('priority');
    }
    /**
     * Returns an array of priority names indexed by ID
     * @return string[] The array of indexed priority named
     */
    public function getAllPriorityNames()
    {
        $names = array();
        $priorities = self::getAll();
        foreach($priorities as $priority) {
            $names[$priority->id] = $priority->name;
        }
        return $names;
    }
}
