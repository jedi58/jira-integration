<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\Authentication;
/**
 * A class used for communicating with the Atlassian Jira RESTful API
 */
abstract class JiraConnection {
    /**
     * @var Authentication Reference to instance of Authentication singleton
     */
    protected $authentication = null;
    /**
     * @var string The result of the last API call. Currently unused
     */
    protected $result = '';
    /**
     * @var int The HTTP status code from the last API call
     */
    protected $last_response_code = 0;
    /**
     * @var bool Flag indicating functions should throw exception on API failure
     */
    protected $shouldExceptionOnError = false;
    /**
     * Default constructor for JiraIntegration
     */
    public function __construct()
    {
        $this->authentication = Authentication::getInstance();
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
     * Sends the request to the Jira API. The response code is also
     * stored in the class.
     * @param string $url The path  to determine request being made
     * @param string[] $data The data to use with the request
     * @param string $method The type of request to make. Default: GET
     * @return StdClass Object containing the returned data
     */
    protected function sendRequest($url, $data = array(), $method = 'GET')
    {
        $ch = curl_init();
        curl_setopt(
                $ch, CURLOPT_URL, $this->authentication->getApiBaseUrl() . '/rest/api/latest/' . $url
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: Basic ' . $this->authentication->getApiAuth(),
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
