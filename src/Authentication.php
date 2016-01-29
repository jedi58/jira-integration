<?php

namespace Inachis\Component\JiraIntegration;
/**
 * Class to be used as a singleton to provide authentication for
 * requests to the Jira API
 */
class Authentication {
	/**
	 * @var Authentication Reference to instance of self
	 */
	private static $instance;
    /**
     * @var string The URL where the Jira API is located
     */
    protected $api_base_url = '';
    /**
     * @var string The base64 encoded username:password pair to use for
     *		authentication
     */
    protected $api_auth = '';
	/**
	 * Returns a singleton instance of this class
	 * @param string $url The URL of the Jira API
	 * @param string $username The username to authenticate with
	 * @param string $password The password to authenticate with
	 * @return Authentication The singleton instance
	 */
	public static function getInstance(
		$url = '',
		$username = '',
		$password = ''
	) {
		if (null === static::$instance) {
			static::$instance = new static($url, $username, $password);
		}
		return static::$instance;
	}
	/**
	 * Function is protected to disallow instantiation using 
	 * new Authentication()
	 * @param string $url The URL of the Jira API
	 * @param string $username The username to authenticate with
	 * @param string $password The password to authenticate with
	 */
	protected function __construct(
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
	 * Specified as private to disallow copying of instance
	 */
	private function __clone() { }
	/**
	 * Specified as private to disallow serialisation of instance
	 */
	private function __wakeup() { }
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
     * Combines the username and password and sets {@link api_auth} to
     * the base64 encoded result
     * @param string $username The username to use
     * @param string $password The password to use
     */
    public function authenticate($username, $password)
    {
        $this->setApiAuth(base64_encode($username . ':' . $password));
    }
}
