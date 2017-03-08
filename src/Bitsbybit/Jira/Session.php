<?php
namespace Bitsbybit\Jira;

use Bitsbybit\Jira\Http\Client as HttpClient;
use Bitsbybit\Jira\Http\Response as HttpResponse;

class Session
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $sessionName;

    /**
     * @var array
     */
    protected $loginInfo;

    /**
     *
     * @var boolean
     */
    protected $ssl;

    /**
     *
     * @var string
     */
    protected $domain;

    /**
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * Helper static method to create with GuzzleHttp\Client
     *
     * @param $domain
     * @param array $options
     * @return \Bitsbybit\Jira\Session
     */
    public static function create($domain, $options=[])
    {
        $httpClient = new HttpClient(__DIR__ . '/jira-cookie.txt');
        return new static($httpClient, $domain, $options);
    }

    /**
     *
     * @param HttpClient $httpClient
     * @param string $domain
     * @param array $options
     */
    public function __construct($httpClient, $domain, $options=[])
    {
        $this->client = $httpClient;
        $this->domain = $domain;
        $this->sessionName = "JSESSIONID";
        $this->parseOptions($options);
    }

    /**
     * @param array $options
     */
    protected function parseOptions(array $options)
    {
        //Default to secure connections
        $this->ssl = true;
        if( isset($options['ssl']) && is_bool($options['ssl'])){
            $this->ssl = $options['ssl'];
        }
        //Perhaps you have a saved Session Id in external storage
        if( isset($options['sessionId']) && isset($options['sessionName']) ){
            $this->sessionId = $options['sessionId'];
            $this->sessionName = $options['sessionName'];
        }
    }

    /**
     * Build the URL for the request
     *
     * @param string $resource
     * @return string
     */
    public function getUrl($resource)
    {
        return ( $this->ssl ? 'https' : 'http' ) . '://' . $this->domain  . $resource;
    }

    /**
     * @param HttpResponse $response
     */
    public function parseLoginResponse(HttpResponse $response)
    {
        $parsedBody = $response->getJsonBody();
        $this->loginInfo = $parsedBody['loginInfo'];
      
        $cookies = $response->getHeaders()['Set-Cookie'];
        foreach($cookies as $cookie){
            if( strpos($cookie, "JSESSIONID") !== false ){
                $parts = explode(';',$cookie);
                foreach($parts as $part){
                    if( strpos($part, 'JSESSIONID') !== false ){
                        $itemParts = explode('=', $part);
                        $this->sessionId = $itemParts[1];
                    }
                }
            }
        }
    }
    
    /**
     * Login to Jira Cloud using a username and password.
     *
     * @param string $username
     * @param string $password
     * @return boolean
     *
     * @throws SessionException
     */
    public function login($username, $password)
    {
        $url = $this->getUrl(Urls::V1_SESSION);

        $response = $this->client->request('POST', $url, [
            'json' => [ 'username' => $username, 'password' => $password ]
        ]);
        
        if( $response->getCode() != 200 ){
            throw new SessionException($response);
        }
      
        $this->parseLoginResponse($response);
      
        return true;
    }

    /**
     * @param $issueKey
     */
    public function getIssue($issueKey)
    {
        $url = $this->getUrl(Urls::V2_ISSUE) . "/" . $issueKey;

        $res = $this->client->request('GET', $url, [
            'cookies' => [ $this->sessionName => $this->sessionId ]
        ]);
        
        return $res->getJsonBody();
    }
}