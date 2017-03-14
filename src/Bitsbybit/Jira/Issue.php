<?php
namespace Bitsbybit\Jira;

class Issue
{
    private $id;
    private $issueKey;
    private $resolution;
    private $links;
    private $properties;
    
    
    public function __construct($properties)
    {
        $this->id = $properties['id'];
        $this->issueKey = $properties['key'];
        $this->resolution = $properties['resolution'];
        $this->links = $properties['issueLinks'];
        $this->properties = $properties;
    }
    
    public function getLinkedIssues()
    {
        
    }
}