<?php
namespace Bitsbybit\Jira\Http;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $headerSize;

    /**
     * @var \Bitsbybit\Jira\Http\Response
     */
    private $response;

    public function setUp()
    {
        $this->client = $this->getMockBuilder('\Bitsbybit\Jira\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->text = <<<'EOD'
HTTP/1.1 200 OK
Server: nginx
Date: Thu, 09 Mar 2017 03:27:58 GMT
Content-Type: application/json;charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
Vary: Accept-Encoding
X-AREQUESTID: 1347x110689x1
Set-Cookie: atlassian.xsrf.token=BCSV-JET9-JCQS-W9MT|5c4832aadb2a0837af1eb9adc4bd192190a479b1|lin; Path=/; Secure
{ "id" : "12341231", "issueKey" : "AT-100" }
EOD;
        $this->headerSize = strlen($this->text) - strlen('{ "id" : "12341231", "issueKey" : "AT-100" }');

        $this->response = new \Bitsbybit\Jira\Http\Response($this->client, $this->text);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Response::getCode
     */
    public function testGetCode()
    {
        $expectedValue = 123;

        $this->client->expects($this->once())
            ->method('getResponseCode')
            ->willReturn($expectedValue);

        $value = $this->response->getCode();
        $this->assertSame($expectedValue, $value);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Response::getBody
     */
    public function testGetBody()
    {
        $this->client->expects($this->once())
            ->method('getResponseHeaderSize')
            ->willReturn($this->headerSize);

        $body = $this->response->getBody();
        $this->assertEquals('{ "id" : "12341231", "issueKey" : "AT-100" }', $body);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Response::getJsonBody
     */
    public function testGetJsonBody()
    {
        $this->client->expects($this->once())
            ->method('getResponseHeaderSize')
            ->willReturn($this->headerSize);

        $body = $this->response->getJsonBody();
        $this->assertEquals([ "id" => "12341231", "issueKey" => "AT-100" ], $body);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Response::getJsonBody
     */
    public function testGetJsonBodyAsObject()
    {
        $this->client->expects($this->once())
            ->method('getResponseHeaderSize')
            ->willReturn($this->headerSize);

        $body = $this->response->getJsonBody(false);
        $expectedObject = new \StdClass;
        $expectedObject->id = "12341231";
        $expectedObject->issueKey = "AT-100";
        $this->assertEquals($expectedObject, $body);
    }

    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Response
     */
    public function testGetHeaders()
    {
        $this->client->expects($this->once())
            ->method('getResponseHeaderSize')
            ->willReturn($this->headerSize);

        $headers = $this->response->getHeaders();

        $this->assertEquals('HTTP/1.1 200 OK',$headers['http_code']);
        $this->assertCount(9, $headers);
    }
}