<?php
namespace Bitsbybit\Jira;

use \GuzzleHttp\Client as GuzzleClient;
use \GuzzleHttp\Exception\RequestException;

use \Bitsbybit\Jira\Urls;
use \Bitsbybit\Jira\Exception as JiraException;
use \Bitsbybit\Jira\Session\Exception as JiraSessionException;
use \Bitsbybit\Jira\Session\Session;

class Jira
{
    /**
     * 
     * @var boolean
     */
    private $ssl;
    
    /**
     * 
     * @var string
     */
    private $domain;
    
    /**
     * 
     * @var string
     */
    private $username;
    
    /**
     * 
     * @var string 
     */
    private $password;
    
    /**
     * 
     * @var GuzzleClient
     */
    private $client;
    
    /**
     * 
     * @param string $domain
     * @param string $username
     * @param string $password
     * @param boolean $ssl
     */
    public function __construct($domain, $username, $password, $ssl=true)
    {
        $this->domain = $domain;
        $this->username = $username;
        $this->password = $password;
        $this->ssl = $ssl;
        $this->client = new GuzzleClient();
    }
    
    /**
     * Build the URL for the request
     * 
     * @param string $resource
     * @return string
     */
    public function getUrl($resource)
    {
        return ( $this->ssl ? 'https' : 'http' ) . '://' . $this->domain . '/' . $resource;
    }
    
    /**
     * @throws JiraException
     * @throws JiraSessionException
     * @return Jira
     */
    public function createSession()
    {
        
        $body = [ 'username' => $this->username, 'password' => $this->password ];
        $url = $this->getUrl(Urls::V1_SESSION);
        
        try{
            $res = $this->client->request('POST', $url, [
                'body' => json_encode($body)
            ]);
        } catch (RequestException $e){
            throw new JiraException("Request Error: " . $e->getMessage(), $e->getCode());
        }
        
        $code = $res->getStatusCode();
        if( $code == 401 ){
            throw new JiraSessionException("Invalid Credentials",401);
        }else if ( $code == 403 ){
            throw new JiraSessionException("Login is denied due to a CAPTCHA requirement, throtting, or any other reason. In case of a 403 status code it is possible that the supplied credentials are valid but the user is not allowed to log in at this point in time.", 403);            
        }
        
        $this->session = new Session($res);
        return $this;
    }
}