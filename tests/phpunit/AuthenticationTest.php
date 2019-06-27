<?php

namespace Inachis\Tests\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\Authentication;
use PHPUnit\Framework\TestCase;

/**
 * @Entity
 * @group unit
 */
class AuthenticationTest extends TestCase
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

    public function testGetUsername()
    {
        $this->assertEquals(
            $this->auth->getUsername(),
            'user'
        );
    }

    public function testSetApiBaseUrl()
    {
        $this->auth->setApiBaseUrl('http://test2.jira');
        $this->assertEquals($this->auth->getApiBaseUrl(), 'http://test2.jira');
    }

    public function testSetUsername()
    {
        $this->auth->setUsername('test');
        $this->assertEquals($this->auth->getUsername(), 'test');
    }

    public function getGetAuthenticationString()
    {
        $this->assertEquals(
            $this->auth->getAuthenticationString(),
            'user2:pass2'
        );
    }
}
