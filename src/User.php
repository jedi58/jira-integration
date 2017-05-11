<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with User resources from the
 * Jira API
 */
class User extends JiraConnection
{
    /**
     * @var Authentication Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return User The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Returns an array of the assignable users for a project
     * @param string[] $options The settings to apply to the search
     * @return stdClass The object containing all assignable users
     */
    public function getAll($options = array())
    {
        return $this->sendRequest(
            'user/assignable/multiProjectSearch?' . http_build_query($options)
        );
    }
    /**
     * Returns an array of the assignable users for a project
     * @param string[] $options The settings to apply to the search
     * @return stdClass The object containing all assignable users
     */
    public function getAllForProject($options = array())
    {
        return $this->sendRequest(
            'user/assignable/search?' . http_build_query($options)
        );
    }
}
