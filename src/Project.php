<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;
/**
 * Project object for interacting with the project namespace of the Jira API
 */
class Project extends JiraConnection {
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
     * @return stdClass The result of creating the project
     */
    public function createProject($key, $name, $lead, $options = array())
    {
        return $this->sendRequest(
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
    }
    /**
     * Updates project in Jira
     * @param string $project_key The "key" for the project to change
     * @param string $name The name of the project to create. 80chars max
     * @param string[] $options Array of additional options to apply to project
     * @return stdClass The result of updating the project
     */
    public function updateProject($project_key, $name, $options = array())
    {
        $options['name'] = $name;
        return $this->sendRequest(
            'project/' . urlencode($project_key),
            $options,
            'PUT'
        );
    }
    /**
     * Deletes a project in Jira
     * @param string $project_key The "key" for the project to delete
     * @return stdClass The result of deleting the project
     */
    public function deleteProject($project_key)
    {
        return $this->sendRequest(
            'project/' . urlencode($project_key),
            array(),
            'DELETE'
        );      
    }
    /**
     * Retrieves a project in Jira
     * @param string $project_key The "key" for the project to return
     * @return stdClass The requested project
     */
    public function getProject($project_key)
    {
        return $this->sendRequest('project/' . urlencode($project_key));
    }
    /**
     * Returns an array of all projects from Jira
     * @return stdClass The requested project
     */
    public function getAllProjects()
    {
        return $this->sendRequest('project');
    }
}
