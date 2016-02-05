<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

class IssueType extends JiraConnection
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
    public function createIssueType($name, $description = '', $type = '')
    {
        $result = $this->sendRequest(
            'issuetype',
            array(
                'name' => $name,
                'description' => $description,
                'type' => $type
            ),
            'POST'
        );
        $response = $this->getLastResponseCode();
        if ($response === 201) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Request invalid');
                    break;
                case 401:
                    throw new \Exception('User not authenticated');
                    break;
                case 403:
                    throw new \Exception('User does not have permissions');
                    break;
                case 409:
                    throw new \Exception('Issue already exists with this name');
                    break;
            }
        }
        return array();
    }
    /**
     *
     */
    public function updateIssueType($type_key, $name, $description = '', $type = '')
    {
        $result = $this->sendRequest(
            'issuetype/' . urlencode($type_key),
            array(
                'name' => $name,
                'description' => $description,
                'type' => $type
            ),
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Request invalid');
                    break;
                case 401:
                    throw new \Exception('User not authenticated');
                    break;
                case 403:
                    throw new \Exception('User does not have permissions');
                    break;
                case 404:
                    throw new \Exception('Issue type not found');
                    break;
                case 409:
                    throw new \Exception('Issue already exists with this name');
                    break;
            }
        }
        return array();
    }
    /**
     *
     */
    public function deleteIssueType($type_key)
    {
        $result = $this->sendRequest(
            'issuetype/' . urlencode($type_key),
            array(),
            'DELETE'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Request invalid');
                    break;
                case 401:
                    throw new \Exception('User not authenticated');
                    break;
                case 403:
                    throw new \Exception('User does not have permissions');
                    break;
                case 404:
                    throw new \Exception('Issue type not found');
                    break;
            }
        }
        return array();
    }
    /**
     *
     */
    public function getAllIssueTypes()
    {
        return $this->sendRequest('issuetype');
    }
    /**
     *
     */
    public function getIssueType($type_key)
    {
        $result = $this->sendRequest('issuetype/' . urlencode($type_key));
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Issue type does not exist');
        }
        return array();
    }
}
