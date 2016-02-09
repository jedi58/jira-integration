<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\Authentication;

/**
 * A class used for communicating with the Atlassian Jira RESTful API
 */
abstract class JiraConnection
{
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
    protected $lastResponseCode = 0;
    /**
     * @var bool Flag indicating functions should throw exception on API failure
     */
    protected $useExceptions = false;
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
    public function getResult($jsonEncode = false)
    {
        return $jsonEncode ? json_encode($this->result) : $this->result;
    }
    /**
     * Returns the value of {@link last_response_code}
     * @return int The value of {@link last_response_code}
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }
    /**
     * Returns the value of {@link useExceptions}
     * @return bool The value of {@link useExceptions}
     */
    public function getUseExceptions()
    {
        return $this->useExceptions;
    }
    /**
     * Sets the value of {@link result}
     * @param string $value The value to set for {@link result}
     * @param bool $json_decode Flag indicating if value should be JSON decoded
     *      when assigned
     */
    public function setResult($value, $jsonDecode = false)
    {
        $this->result = $jsonDecode ? json_decode($value) : $value;
    }
    /**
     * Sets the value of {@link last_response_code}
     * @param int $value The value to set for {@link last_response_code}
     */
    public function setLastResponseCode($value)
    {
        $this->lastResponseCode = (int) $value;
    }
    /**
     * Sets the value of {@link useExceptions}
     * @param bool $value The value to set for {@link useExceptions}
     */
    public function setUseExceptions($value)
    {
        $this->useExceptions = (bool) $value;
    }
    /**
     * Sends the request to the Jira API. The response code is also
     * stored in the class.
     * @param string $url The path  to determine request being made
     * @param string[] $data The data to use with the request
     * @param string $method The type of request to make. Default: GET
     * @param bool $multipart Flag indicating if this is a multipart/attachment request
     * @return StdClass Object containing the returned data
     */
    protected function sendRequest($url, $data = array(), $method = 'GET', $multipart = false)
    {
        $jiraConn = curl_init();
        curl_setopt(
            $jiraConn,
            CURLOPT_URL,
            $this->authentication->getApiBaseUrl() . '/rest/api/latest/' . $url
        );
        $headers = array(
            'Content-type: application/json',
            'Authorization: Basic ' . $this->authentication->getApiAuth(),
        );
        if ($multipart) {
            $headers[0] = 'Content-type: multipart/form-data';
            $headers[] = 'X-Atlassian-Token: no-check';
        }
        curl_setopt($jiraConn, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($jiraConn, CURLOPT_HEADER, false);
        curl_setopt($jiraConn, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($jiraConn, CURLOPT_RETURNTRANSFER, true);
        if ($method == 'POST') {
            curl_setopt($jiraConn, CURLOPT_POST, true);
            curl_setopt($jiraConn, CURLOPT_POSTFIELDS, $multipart ? $data : json_encode($data));
        } else {
            curl_setopt($jiraConn, CURLOPT_HTTPGET, $data);
        }
        $result = json_decode(curl_exec($jiraConn));
        $this->setLastResponseCode(curl_getinfo($jiraConn, CURLINFO_HTTP_CODE));
        curl_close($jiraConn);
        $responseCode = $this->getLastResponseCode();
        if ($responseCode < 300 && $this->getUseExceptions()) {
            throw new \Exception($this->getHTTPStatusCodeAsText($responseCode));
        }
        return $result;
    }
    /**
     * Returns descriptive text for the provided HTTP response code
     * @param string $code The HTTP status code returns by the API request
     * @return string The descriptive error for the status code
     */
    protected function getHTTPStatusCodeAsText($code)
    {
        switch ($code) {
            case 400:
                $message = 'Invalid request';
                break;

            case 401:
                $message = 'Request not authenticated';
                break;

            case 403:
                $message = 'Permission denied';
                break;

            case 404:
                $message = 'Resource not found';
                break;

            case 409:
                $message = 'Format is not supported or name already in use';
                break;

            case 412:
                $message = 'If-Match header is not null and does not match server';
                break;

            default:
                $message = 'Undefined error';
        }
        return $message;
    }
    /**
     * Returns an aray where the value is associated with id or key
     * @param string $value The value being tested
     * @param string $field The name of the field for the key
     * @return string[] The associative array
     */
    protected function specifyIdOrKey($value, $field = 'key')
    {
        return array(is_numeric($value) ? 'id' : $field => $value);
    }
}
