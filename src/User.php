<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;
/**
 *
 */
class User extends JiraConnection {
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
     * Returns an array of the assignable users for a project
     * @param string[] $options The settings to apply to the search
     * @return
     */
    public function getAssignableUsers($options = array())
    {
        return $this->sendRequest(
            'user/assignable/search?' . http_build_query($options)
        );
    }
}
