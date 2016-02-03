<?php

namespace Inachis\Tests\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\Authentication;
/**
 * @Entity
 * @group unit
 */
class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
	protected $auth;

	public function setUp()
	{
		$this->auth = Authentication::getInstance(
			'http://test.jira',
			'user',
			'pass'
		);
	}

	public function testGetApiBaseUrl()
	{
		$this->assertEquals($this->auth->getApiBaseUrl(), 'http://test.jira');
	}

	public function testGetApiAuth()
	{
		$this->assertEquals($this->auth->getApiAuth(), 
			base64_encode('user:pass'));
	}

	public function testSetApiBaseUrl()
	{
		$this->auth->setApiBaseUrl('http://test2.jira');
		$this->assertEquals($this->auth->getApiBaseUrl(), 'http://test2.jira');
	}

	public function testSetApiAuth()
	{
		$this->auth->setApiAuth('test');
		$this->assertEquals($this->auth->getApiAuth(), 'test');
	}

	public function testAuthenticate()
	{
		$this->auth->authenticate('user2', 'pass2');
		$this->assertEquals(
			$this->auth->getApiAuth(),
			base64_encode('user2:pass2')
		);
	}
}
