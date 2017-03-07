<?php
namespace tests\Jira\Session;

use \Bitsbybit\Jira\Urls;
use \Bitsbybit\Jira\Session\Session as JiraSession;

class SessionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private $domain = "example.bitsbybit.com";

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     *
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session\Session::getUrl
     */
    public function testHttpsGetUrl()
    {
        $jira = new JiraSession($this->client, $this->domain);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("https://example.bitsbybit.com/jira/rest/api", $url);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session\Session::getUrl
     */
    public function testHttpGetUrl()
    {
        $jira = new JiraSession($this->client, $this->domain, false);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("http://example.bitsbybit.com/jira/rest/api", $url);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session\Session::login
     * @expectedException \Bitsbybit\Jira\Session\Exception
     *
     */
    public function testFailedLogin()
    {
        $requestException = $this->getMockBuilder('\GuzzleHttp\Exception\RequestException')
            ->disableOriginalConstructor()
            ->getMock();

        $username = 'testuser';
        $password = 'testpass';
        $jira = new JiraSession($this->client, $this->domain);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($requestException);

        $jira->login($username, $password);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session\Session::login
     *
     */
    public function testSuccessfulLogin()
    {
        $username = 'testuser';
        $password = 'testpass';
        $jira = new JiraSession($this->client, $this->domain);
        $url = $jira->getUrl(Urls::V1_SESSION);

        $request = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->getMock();

        $this->client->expects($this->once())
            ->method('request')
            ->with('POST', $url, [
                'json' => [ 'username' => $username, 'password' => $password ]
            ])
            ->willReturn($request);

        $result = $jira->login($username, $password);
    }


//    /**
//     * @test
//     * @covers \Bitsbybit\Jira\Session\Session::login
//     *
//     */
//    public function testLogin()
//    {
//        $httpClient = new \GuzzleHttp\Client();
//        $jira = new JiraSession($httpClient, '*.atlassian.net', true);
//        $jira->login('','');
//    }
}