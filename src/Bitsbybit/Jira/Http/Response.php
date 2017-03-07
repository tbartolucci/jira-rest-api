<?php
namespace Bitsbybit\Jira\Http;

class Response
{
    /**
     * @var resource
     */
    private $ch;

    /**
     * @var string
     */
    private $body;

    public function __construct($ch, $body)
    {
        $this->ch = $ch;
        $this->body = $body;
    }
}