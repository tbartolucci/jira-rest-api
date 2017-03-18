<?php
namespace Bitsbybit\Jira;

class Issue
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $issueKey;
    /**
     * @var array
     */
    private $properties;

    /**
     * Issue constructor.
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->id = $properties['id'];
        $this->issueKey = $properties['key'];
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIssueKey()
    {
        return $this->issueKey;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->properties['fields']['summary'];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->properties['fields']['description'];
    }

    /**
     * @return mixed
     */
    public function __debugInfo()
    {
        return $this->properties;
    }
}
