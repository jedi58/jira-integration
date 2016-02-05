<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with IssueType resources from the
 * Jira API
 */
class IssueType extends JiraConnection
{
    /**
     * Creates a new isue type
     * @param string $name The name of the new issue type
     * @param string $description Descriptive text for the new issue type
     * @param string $type The type of issue type being created
     * @return stdClass The result of creating the issue type
     */
    public function create($name, $description = '', $type = '')
    {
        return $this->sendRequest(
            'issuetype',
            array(
                'name' => $name,
                'description' => $description,
                'type' => $type
            ),
            'POST'
        );
    }
    /**
     * Updates an existing issue type
     * @param string $name The new name of the  issue type
     * @param string $description The new descriptive text for the issue type
     * @param string $type The new type for issue type
     * @return stdClass The result of updating the issue type
     */
    public function update($type_key, $name, $description = '', $type = '')
    {
        return $this->sendRequest(
            'issuetype/' . urlencode($type_key),
            array(
                'name' => $name,
                'description' => $description,
                'type' => $type
            ),
            'PUT'
        );
    }
    /**
     * Deletes a specific issue type
     * @param string $type_key The key identifying the issue type
     * @return stdClass The result of deleting the issue type
     */
    public function delete($type_key)
    {
        return $this->sendRequest(
            'issuetype/' . urlencode($type_key),
            array(),
            'DELETE'
        );
    }
    /**
     * Returns a specific issue type
     * @param string $type_key The identifier for the issue type
     * @return stdClass The object containing the issue type
     */
    public function get($type_key)
    {
        return $this->sendRequest('issuetype/' . urlencode($type_key));
    }
    /**
     * Returns an object containing all issue types available
     * @return stdClass The object containing all issuetypes
     */
    public function getAll()
    {
        return $this->sendRequest('issuetype');
    }
}
