<?php

/**
 * A class used for communicating with the Atlassian Jira RESTful API
 */
class JiraIntegration
{
    /**
     * The URL where the Jira API is located
     */
    protected $api_base_url = '';
    /**
     * The base64 encoded username:password pair to use for authentication
     */
    protected $api_auth = '';
    /**
     * The result of the last API call. Currently unused
     */
    protected $result = '';
    /**
     * The HTTP status code from the last API call
     */
    protected $last_response_code = 0;
    /**
     * Flag indicating functions should throw exception on API failure
     */
    protected $shouldExceptionOnError = false;
    /**
     * Default constructior for JiraIntegration
     * @param string $url The URL of the Jira API to connect to
     * @param string $username The username to connect with
     * @param stirng $password The password to connect with
     */
    public function __construct(
        $url = '',
        $username = '',
        $password = ''
    ) {
        $this->setApiBaseUrl($url);
        if (!empty($username) && !empty($password)) {
            $this->authenticate($username, $password);
        }
    }
    /**
     * Returns the value of {@link api_base_url}
     * @return string The value of {@link api_base_url}
     */
    public function getApiBaseUrl()
    {
        return $this->api_base_url;
    }
    /**
     * Returns the value of {@link api_auth}
     * @return string The value of {@link api_auth}
     */
    public function getApiAuth()
    {
        return $this->api_auth;
    }
    /**
     * Returns the value of {@link api_base_url}
     * @param bool $json_encode Flag indicating if contents should be JSON
     *          encoded when returned
     * @return string The value of {@link api_base_url}
     */
    public function getResult($json_encode = false)
    {
        return $json_encode ? json_encode($this->result) : $this->result;
    }
    /**
     * Returns the value of {@link last_response_code}
     * @return int The value of {@link last_response_code}
     */
    public function getLastResponseCode()
    {
        return $this->last_response_code;
    }
    /**
     * Returns the value of {@link shouldExceptionOnError}
     * @return bool The value of {@link shouldExceptionOnError}
     */
    public function getShouldExceptionOnError ()
    {
        return $this->shouldExceptionOnError;
    }
    /**
     * Sets the value of {@link api_base_url}
     * @param string $value The URL to set {@link api_base_url} to
     */
    public function setApiBaseUrl($value)
    {
        $this->api_base_url = $value;
    }
    /**
     * Sets the value of {@link api_auth}
     * @param string $value The string to set {@link api_auth} to
     */
    public function setApiAuth($value)
    {
        $this->api_auth = $value;
    }
    /**
     * Sets the value of {@link result}
     * @param string $value The value to set for {@link result}
     * @param bool $json_decode Flag indicating if value should be JSON decoded
     *      when assigned
     */
    public function setResult($value, $json_decode = false)
    {
        $this->result = $json_decode ? json_decode($value) : $value;
    }
    /**
     * Sets the value of {@link last_response_code}
     * @param int $value The value to set for {@link last_response_code}
     */
    public function setLastResponseCode($value)
    {
        $this->last_response_code = (int) $value;
    }
    /**
     * Sets the value of {@link shouldExceptionOnError}
     * @param bool $value The value to set for {@link shouldExceptionOnError}
     */
    public function setsSouldExceptionOnError($value)
    {
        $this->shouldExceptionOnError = (bool) $value;
    }
    /**
     * Combines the username and password and sets {@link api_auth} to
     * the base64 encoded result
     * @param string $username The username to use
     * @param string $password The password to use
     */
    public function authenticate($username, $password)
    {
        $this->setApiAuth(base64_encode($username . ':' . $password));
    }
    /**
     * Creates a new ticket
     * @param string[] Settings to apply to the ticket
     * @return string[] The key of the created ticked. e.g. DEMO-123
     */
    public function createTicket($data = array())
    {
        $result = $this->sendRequest('issue', $data, 'POST');
        if ($this->getLastResponseCode() !== 201) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to create Jira ticket');
            } else {
                return array('key' => null);
            }
        }
        return array('key' => $result->key);
    }
    /**
     * Simple interface for {@link createTicket} but provides
     * less flexibility.
     * @param string $project The project to assign the new ticket to
     * @param string $title The title of the new ticket
     * @param string $description A description for the new ticket
     * @param string $issuetype The type of ticket being created
     * @param string[] $timetracking Time estimates for the ticket
     * @param string[] $custom Any custom properties to assign to the ticket
     * @return string[] The key of the created ticket. e.g. DEMO-123
     */
    public function simpleCreateTicket(
        $project,
        $title,
        $description,
        $issuetype = 'Bug', 
        $timetracking = array(),
        $custom = array()
    ) {
        $data = array();
        $data['fields'] = array_merge(
            array(
                'project' => array(),
                'summary' => $title,
                'description' => $description,
                'issuetype' => array()
            ),
            $custom
        );
        if (is_numeric($project)) {
            $data['fields']['project']['id'] = $project;
        } else {
            $data['fields']['project']['key'] = $project;
        }
        if (is_numeric($issuetype)) {
            $data['fields']['issuetype']['id'] = $issuetype;
        } else {
            $data['fields']['issuetype']['name'] = $issuetype;
        }
        if (isset($data['timetracking']) && !empty($timetracking)) {
            $data['fields']['timetracking'] = $timetracking;
        }
        return $this->createTicket($data);
    }
    /**
     * Updates the specified Jira ticket
     * @param string $issue_key The ticket to be update
     * @param string[] $data The changes to make to the ticket
     * @return bool Returns TRUE if successful
     */
    public function updateTicket($issue_key, $data)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            $data,
            'PUT'
        );
        if ($this->getLastResponseCode() !== 204) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to update Jira ticket');
            } else {
                return false;
            }
        }
        return true;
    }
    /**
     * Deletes the specified ticket
     * @param string $issue_key The ticket to be removed
     * @param bool $remove_subtasks Flag indicating if sub-tasks can be removed
     * @return bool Returns TRUE if deletion was successful
     */
    public function deleteTicket($issue_key, $remove_subtasks = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(
                'deleteSubtasks' => (string) $remove_subtasks 
            ),
            'DELETE'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 401:
                    throw new \Exception('Error deleting ticket: User not authenticated');
                    break;

                case 403:
                    throw new \Exception('Error deleting ticket: Permission denied');
                    break;

                case 404:
                    throw new \Exception('Error deleting ticket: Does not exist');
                    break;

                case 400:
                default:
                    throw new \Exception('Error deleting ticket');
            }
        }
        return false;
    }
    /**
     * Retrieves the specifed ticket
     * @param string $issue_key The ticket to be returned
     * @return StdClass The object containing the ticket
     */
    public function getTicket($issue_key)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key),
            array(),
            'GET'
        );
        if ($this->getLastResponseCode() !== 204
                && $this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket could not be returned');
        }
        return $result;
    }
    /**
     * Assigns the specified user to the ticket. A name should be passed here
     * otherwise it will assign the automatic assignee. Using null wil make
     * the ticket unassigned
     * @param string $issue_key The ticket to change assignee of
     * @param string $assignee The name of the user to assign the ticket to
     * @param bool The result of attempting to assign the user
     */
    public function assignTicket($issue_key, $assignee = '-1')
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/assignee',
            array(
                'name' => $assignee
            ),
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            switch ($response) {
                case 401:
                    throw new \Exception('Error assigning user: Permission denied');
                    break;

                case 404:
                    throw new \Exception('Error assigning user: Issue or user not found');
                    break;

                case 400:
                default:
                    throw new \Exception('Error assigning user to ticket');
            }
        }
        return false;
    }
    /**
     * Adds a comment to the specified ticket
     * @param string $issue_key The ticket to be updated
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return string[] The timestamp the comment was added
     */
    public function addComment($issue_key, $text, $visibility = null)
    {
        $data - array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment', 
            $data,
            'POST'
        );
        if ($this->getLastResponseCode() >= 300) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to add comment.');
            } else {
                return array('updated' => null);
            }
        }
        return array('updated' => $result->updated);
    }
    /**
     * Updated the specified comment
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return string[] The timestamp the comment was updated
     */
    public function updateComment(
        $issue_key,
        $comment_id,
        $text,
        $visibility = null,
        $expand = false
    ) {
        $data - array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        if ($expand) {
            $data['expand'] = 'true';
        }
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $data,
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return array('updated' => $result->updated);
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Failed to update comment');
        }
        return array('updated' => $result->updated);
    }
    /**
     * Deletes the specified comment from the issue
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @return bool The result of deleting the comment from the issue
     */
    public function deleteComment($issue_key, $comment_id)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            array(),
            'DELETE'
        );
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Failed to update comment');
        }
        return false;
    }
    /**
     * Returns an array of all commments for the specified issue
     * @param string $issue_key The issue to get comments for
     * @param bool $expand Flag indicating if comment should be HTML
     * @return string[] Array of comments for the given issue
     */
    public function getAllComments($issue_key, $expand = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment',
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket comments could not be returned');
        }
        return array(
            'startAt' => 0,
            'maxResults' => 0,
            'total' => 0,
            'comments' => array()
        );
    }
    /**
     * Returns a specific comment for an issue
     * @param string $issue_key The issue to get comments for
     * @param string $comment_id The ID of the comment to retrieve
     * @param bool $expand Flag indicating if comment should be HTML
     * @return string[] Array of comments for the given issue limited by
     *                   {@link comment_id}
     */
    public function getComment($issue_key, $comment_id, $expand = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket comments could not be returned');
        }
        return array(
            'startAt' => 0,
            'maxResults' => 0,
            'total' => 0,
            'comments' => array()
        );
    }
    /**
     *
     */
    public function getProjects()
    {
        return $this->sendRequest('project');
    }
    /**
     *
     */
    public function getIssueTypes()
    {
        return $this->sendRequest('issuetype');
    }
    /**
     *
     */
    public function getProjectIssueAvailableConfig($projectId)
    {
        return $this->sendRequest(
            'issue/createmeta?projectKeys=' . urlencode($projectId) .
            '&issuetypeName=Bug&expand=projects.issuetypes.fields'
        );
    }
    /**
     *
     */
    public function getCustomFieldOption($field_id)
    {
        return $this->sendRequest('customFieldOption/' . $field_id);
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
    /**
     * Sends the request to the Jira API. The response code is also
     * stored in the class.
     * @param string $url The path  to determine request being made
     * @param string[] $data The data to use with the request
     * @param string $method The type of request to make. Default: GET
     * @return StdClass Object containing the returned data
     */
    private function sendRequest($url, $data = array(), $method = 'GET')
    {
        $ch = curl_init();
        curl_setopt(
                $ch, CURLOPT_URL, $this->getApiBaseUrl() . '/rest/api/latest/' . $url
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: Basic ' . $this->getApiAuth(),
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, $data);
        }
        $result = json_decode(curl_exec($ch));
        $this->setLastResponseCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
        return $result;
    }
}
