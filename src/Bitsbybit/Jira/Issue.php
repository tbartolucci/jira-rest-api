<?php
namespace Bitsbybit\Jira;

class Issue
{
    private $id;
    private $issueKey;
    private $properties;
    
    
    public function __construct($properties)
    {
        $this->id = $properties['id'];
        $this->issueKey = $properties['key'];
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function __debugInfo()
    {
        return $this->properties;
    }
}
