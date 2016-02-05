<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 *
 */
class Permission extends JiraConnection
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
     *
     */
    public function getPermissions()
    {
        return $this->sendRequest('mypermissions');
    }
    /**
     *
     */
    public function getAllPermissions()
    {
        $result = $this->sendRequest('permissions');
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Failed to return permissions. Permission denied');
        }
        return array('permissions' => null);
    }
}
