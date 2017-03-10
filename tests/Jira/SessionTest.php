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
        $jira = new JiraSession($this->client, $this->domain, [ 'ssl' => false]);

        $url = $jira->getUrl("/jira/rest/api");
        $this->assertEquals("http://example.bitsbybit.com/jira/rest/api", $url);
    }
}