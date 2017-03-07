<?php
namespace tests\Jira;

use \Bitsbybit\Jira\Urls;
use \Bitsbybit\Jira\Session as JiraSession;
use GuzzleHttp\Exception\ClientException;

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
     * @covers \Bitsbybit\Jira\Session::getUrl
     */
    public function testHttpsGetUrl()
    {
        $jira = new JiraSession($this->client, $this->domain);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("https://example.bitsbybit.com/jira/rest/api", $url);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::getUrl
     */
    public function testHttpGetUrl()
    {
        $jira = new JiraSession($this->client, $this->domain, false);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("http://example.bitsbybit.com/jira/rest/api", $url);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::login
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
     * @covers \Bitsbybit\Jira\Session::login
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
        $this->assertTrue($result);
    }


    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::login
     *
     */
    public function testLogin()
    {
        $jira = JiraSession::create('*.atlassian.net', [
            'ssl' => true
        ]);
        try {
            $jira->login('', '');
        }catch(\Exception $e){
            echo "MESSAGE: ".$e->getMessage();
        }

        echo "RESPONSE".PHP_EOL;

//        try {
//            $jira->getIssue("HI-762");
//        }catch(\Exception $e){
//            echo PHP_EOL."[".$e->getCode()."] ". $e->getMessage(). PHP_EOL;
//            echo $e->getTraceAsString();
//        }
    }
}