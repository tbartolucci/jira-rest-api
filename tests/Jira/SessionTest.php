<?php
namespace tests\Jira;

use \Bitsbybit\Jira\Session as JiraSession;

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
        $this->client = $this->getMockBuilder('\Bitsbybit\Jira\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::create
     */
    public function testCreateMethod()
    {
        $instance = \Bitsbybit\Jira\Session::create('example.domain.com','/path/to/cookie.txt');
        $this->assertInstanceOf('\Bitsbybit\Jira\Session', $instance);
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::__construct
     * @covers \Bitsbybit\Jira\Session::parseOptions
     */
    public function testConstructor()
    {
        $session = new \Bitsbybit\Jira\Session($this->client, 'example.domain.com', [
            'ssl' => true,
            'sessionId' => 'ABCDDFDABA1234ADF',
            'sessionName' => 'JSESSIONID'
        ]);
        $this->assertInstanceOf('\Bitsbybit\Jira\Session', $session);
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
        $jira = new JiraSession($this->client, $this->domain, [ 'ssl' => false]);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("http://example.bitsbybit.com/jira/rest/api", $url);
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::parseLoginResponse
     */
    public function testParseLoginResponse()
    {
        $expectedSessionId = '5c4832aadb2a0837af1eb9adc4bd192190a479b1';
        $expectedLoginInfo = [ 
            'loginCount' => 6743,
            'lastLogin' => date('Y-m-d')
        ];
        
        $responseArray = [ 
            'loginInfo' => $expectedLoginInfo
        ];
        
        $headersArray = [
            'http_code' => 'HTTP/1.1 200 OK',
            'Server' => 'nginx',
            'Date' => 'Thu, 09 Mar 2017 03:27:58 GMT',
            'Content-Type' => 'application/json',
            'Set-Cookie' => [
                'JSESSIONID='.$expectedSessionId
            ]
        ];
        
        $response = $this->getMockBuilder('\Bitsbybit\Jira\Http\Response')
            ->disableOriginalConstructor()
            ->getMock();
        
        $response->expects($this->once())
            ->method('getJsonBody')
            ->willReturn($responseArray);
        
        $response->expects($this->once())
            ->method('getHeaders')
            ->willReturn($headersArray);
            
        $jira = new JiraSession($this->client, $this->domain);
        $jira->parseLoginResponse($response);
        $this->assertEquals($expectedSessionId, $jira->getSessionId());
        $this->assertEquals($expectedLoginInfo, $jira->getLoginInfo());
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Session::login
     */
    public function testLogin()
    {
        $username = 'user1';
        $password = 'BadPassword';
        
        $response = $this->getMockBuilder('\Bitsbybit\Jira\Http\Response')
        ->disableOriginalConstructor()
        ->getMock();
        
        $response->expects($this->once())
            ->method('getCode')
            ->willReturn(200);
        
        $response->expects($this->once())
            ->method('getJsonBody')
            ->willReturn([ 
                'loginInfo' => []
        ]);
            
        $response->expects($this->once())
            ->method('getHeaders')
            ->willReturn([]);
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        
        $jira = new JiraSession($this->client, $this->domain);
        $result = $jira->login($username, $password);
        $this->assertTrue($result);
    }
    
    public function testGetIssue()
    {
        $issueKey = 'AT-100';
        $expectedResponseArray = [ 'issueKey' => $issueKey ];
        
        $response = $this->getMockBuilder('\Bitsbybit\Jira\Http\Response')
            ->disableOriginalConstructor()
            ->getMock();
        
        $response->expects($this->once())
            ->method('getJsonBody')
            ->willReturn($expectedResponseArray);
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($response);
            
        $jira = new JiraSession($this->client, $this->domain);
        $issueArray = $jira->getIssue($issueKey);
        $this->assertSame($expectedResponseArray, $issueArray);
    }
}
