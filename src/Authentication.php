<?php

namespace Inachis\Component\JiraIntegration;

/**
 * Class to be used as a singleton to provide authentication for
 * requests to the Jira API
 */
class Authentication
{
    /**
     * @var Authentication Reference to instance of self
     */
    private static $instance;
    /**
     * @var string The URL where the Jira API is located
     */
    protected $apiBaseUrl = '';
    /**
     * @var string The username to connect with
     */
    protected $username = '';
    /**
     * @var string The token to use for connection
     */
    protected $token = '';
    /**
     * Returns a singleton instance of this class
     * @param string $url The URL of the Jira API
     * @param string $username The username to authenticate with
     * @param string $token The token to authenticate with
     * @return Authentication The singleton instance
     */
    public static function getInstance(
        $url = '',
        $username = '',
        $token = ''
    ) {
        if (null === static::$instance) {
            static::$instance = new static($url, $username, $token);
        }
        return static::$instance;
    }
    /**
     * Function is protected to disallow instantiation using
     * new Authentication()
     * @param string $url The URL of the Jira API
     * @param string $username The username to authenticate with
     * @param string $token The token to authenticate with
     */
    protected function __construct(
        $url = '',
        $username = '',
        $token = ''
    ) {
        $this->setApiBaseUrl($url);
        $this->setUsername($username);
        $this->setToken($token);
    }
    /**
     * Returns the value of {@link api_base_url}
     * @return string The value of {@link api_base_url}
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }
    /**
     * Returns the value of {@link username}
     * @return string The value of {@link username}
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * Returns the value of {@link token}
     * @return string The value of {@link token}
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * Sets the value of {@link api_base_url}
     * @param string $value The URL to set {@link api_base_url} to
     */
    public function setApiBaseUrl($value)
    {
        $this->apiBaseUrl = $value;
    }
    /**
     * Sets the value of {@link username}
     * @param string $value The string to set {@link username} to
     */
    public function setUsername($value)
    {
        $this->username = $value;
    }
    /**
     * Sets the value of {@link token}
     * @param string $value The string to set {@link token} to
     */
    public function setToken($value)
    {
        $this->token = $value;
    }

    public function getAuthenticationString()
    {
        return $this->getUsername() . ':' . $this->getToken();
    }
}
