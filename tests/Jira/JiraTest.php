<?php
namespace tests\Jira;

class JiraTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @covers \Bitsbybit\Jira\Jira::getUrl
     */
    public function testHttpsGetUrl()
    {
        $domain = "example.bitsbybit.com";
        $username = "testuser";
        $password = "Password1";
        
        $jira = new \Bitsbybit\Jira\Jira($domain, $username, $password);
        
        $url = $jira->getUrl("jira/rest/api");
        $this->assertEquals("https://example.bitsbybit.com/jira/rest/api", $url);
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Jira::getUrl
     */
    public function testHttpGetUrl()
    {
        $domain = "example.bitsbybit.com";
        $username = "testuser";
        $password = "Password1";
    
        $jira = new \Bitsbybit\Jira\Jira($domain, $username, $password, false);
    
        $url = $jira->getUrl("jira/rest/api");
        $this->assertEquals("http://example.bitsbybit.com/jira/rest/api", $url);
    }
}