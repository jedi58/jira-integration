<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for handling permission resource requests from
 * the Jira API
 */
class Permission extends JiraConnection
{
    /**
     * @var Authentication Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return Permission The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Retrieves current user's permissions for a given project or issue
     * @param string $projectOrIssue Indicates if request is for project/issue
     * @param string|int $key The numerical Id or key for the the project/issue
     * @return stdClass The requested permissions
     */
    public function get($projectOrIssue = '', $key = '')
    {
        $data = array();
        if (!empty($projectOrIssue) && !empty($key)) {
            $data[$projectOrIssue . is_int($key) ? 'Id' : 'Key'] = $key;
        }
        return $this->sendRequest('mypermissions', $data);
    }
    /**
     * Returns an object containing all permissions
     * @return stdClass The requested permissions
     */
    public function getAll()
    {
        return $this->sendRequest('permissions');
    }
}
