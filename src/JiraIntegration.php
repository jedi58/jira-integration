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
     *
     */
    protected $return_type;
    /**
     *
     */
    protected $api_auth = '';
    /**
     *
     */
    protected $result = '';
    /**
     *
     */
    protected $last_response_code = 0;
    /**
     *
     */
    public function __construct(
        $url = '',
        $username = '',
        $password = ''
    ) {
        $this->setApiBaseUrl($url);
        $this->authenticate($username, $password);
    }
    /**
     *
     */
    public function getApiBaseUrl()
    {
        return $this->api_base_url;
    }
    /**
     *
     */
    public function getApiAuth()
    {
        return $this->api_auth;
    }
    /**
     *
     */
    public function getResult($json_encode = false)
    {
        return $json_encode ? json_encode($this->result) : $this->result;
    }
    /**
     *
     */
    public function getLastResponseCode()
    {
        return $this->last_response_code;
    }
    /**
     *
     */
    public function setApiBaseUrl($value)
    {
        $this->api_base_url = $value;
    }
    /**
     *
     */
    public function setApiAuth($value)
    {
        $this->api_auth = $value;
    }
    /**
     *
     */
    public function setResult($value, $json_decode = false)
    {
        $this->result = $json_decode ? json_decode($value) : $value;
    }
    /**
     *
     */
    public function setLastResponseCode($value)
    {
        $this->last_response_code = $value;
    }
    /**
     *
     */
    public function authenticate($username, $password)
    {
        $this->setApiAuth(base64_encode($username . ':' . $password));
    }
    /**
     * Creates a new ticket
     * @param string[] Settings to apply to the ticket
     * @return 
     */
    public function createTicket($data = array())
    {
        $result = $this->sendRequest('issue', $data, 'POST');
        if ($result < 300) {
            return array('key' => $this->getResult()->key);
        }
        return array('error' => print_r($this->getResult(), true));
    }
    /**
     * Simpleer interface for createTicket() but provides
     * less flexibility.
     * @param
     * @param
     * @param
     * @param
     * @param
     * @param
     * @return
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
            // originalEstimate: '1d 2h 25m'
            // remainingEstimate: ''
        }
        return $this->createTicket($data);
    }
    /**
     *
     */
    public function updateIssue($issue_key, $options)
    {
        $result = $this->sendRequest(
            'issue/' . $issue_key, array(
                'update' => $options
            ), 'POST'
        );
        if ($result < 300) {
            return array('key' => $this->getResult()->key);
        }
        return array('error' => print_r($this->getResult(), true));
    }
    /**
     *
     */
    public function simpleUpdate($issue_key, $description, $options = array())
    {
        $data = array_merge(
            array(
                'description' => array(
                    'set' => $description
                )
            ),
            $options
        );
        return $this->updateIssue($issue_key, $data);
    }
    /**
     *
     */
    public function addComment($issue_key, $text, $visibility = null)
    {
        $result = $this->sendRequest(
            'issue/' . $issue_key . '/comment', 
            array('body' => $text),
            'POST'
        );
        if ($result < 300) {
            return array('updated' => $this->getResult()->updated);
        }
        return array('error' => $this->getResult());
    }
    /**
     *
     */
    public function getTicket($issue_key)
    {
        return $this->sendRequest('issue/' . $issue_key, array());
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
        $result = curl_exec($ch);
        $this->setLastResponseCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
        return $result;
    }
}
