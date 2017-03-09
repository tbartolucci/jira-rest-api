<?php
namespace tests\Jira\Http;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Client::getResponseCode
     */
    public function testGetResponseCode()
    {
        $this->markTestSkipped("This method executes a CURL request and requires external dependencies.");
    }
    
    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Client::getResponseHeaderSize
     */
    public function testGetResponseHeaderSize()
    {
        $this->markTestSkipped("This method executes a CURL request and requires external dependencies.");
    }
    /**
     * @test
     * @covers \Bitsbybit\Jira\Http\Client::request
     */
    public function testRequest()
    {
        $this->markTestSkipped("This method executes a CURL request and requires external dependencies.");
    }
}