<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Project object for interacting with the project namespace of the Jira API
 */
class Project extends JiraConnection
{
    /**
     * @var Authentication Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return Project The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Creates a new project in Jira
     * @param string $key The "key" for the project. 10 chars max
     * @param string $name The name of the project to create. 80chars max
     * @param string $lead The name of the project lead
     * @param string[] $options Array of additional options to apply to project
     * @return string[] The result of creating the project
     * @throws \Exception
     */
    public function createProject($key, $name, $lead, $options = array())
    {
        $result = $this->sendRequest(
            'project',
            array_merge(
                array(
                    'key' => $key,
                    'name' => $name,
                    'lead' => $lead
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
                    throw new \Exception('Invalid request to create project');
                    break;
                case 401:
                    throw new \Exception('User not authenticated');
                    break;
                case 403:
                    throw new \Exception('Permission denied creating project');
                    break;
            }
        }
        return array();
    }
    /**
     * Updates project in Jira
     * @param string $project_key The "key" for the project to change
     * @param string $name The name of the project to create. 80chars max
     * @param string[] $options Array of additional options to apply to project
     * @return string[] The result of updating the project
     * @throws \Exception
     */
    public function updateProject($project_key, $name, $options = array())
    {
        $options['name'] = $name;
        $result = $this->sendRequest(
            'project/' . urlencode($project_key),
            $options,
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 201) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 400:
                    throw new \Exception('Invalid request to create project');
                    break;

                case 401:
                    throw new \Exception('User not authenticated');
                    break;

                case 403:
                    throw new \Exception('Permission denied creating project');
                    break;

                case 404:
                    throw new \Exception('Project not found');
                    break;
            }
        }
        return array();
    }
    /**
     * Deletes a project in Jira
     * @param string $project_key The "key" for the project to delete
     * @return string[] The result of deleting the project
     * @throws \Exception
     */
    public function deleteProject($project_key)
    {
        $result = $this->sendRequest(
            'project/' . urlencode($project_key),
            array(),
            'DELETE'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 401:
                    throw new \Exception('User not authenticated');
                    break;

                case 403:
                    throw new \Exception('Permission denied creating project');
                    break;

                case 404:
                    throw new \Exception('Project not found');
                    break;
            }
        }
        return array();
    }
    /**
     * Retrieves a project in Jira
     * @param string $project_key The "key" for the project to return
     * @return string[] The requested project
     * @throws \Exception
     */
    public function getProject($project_key)
    {
        $result = $this->sendRequest('project/' . urlencode($project_key));
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Project not found');
        }
        return array();
    }
    /**
     * Returns an array of all projects from Jira
     * @return string[] The requested project
     */
    public function getAllProjects()
    {
        return $this->sendRequest('project');
    }
}
