<?php
namespace Bitsbybit\Jira;

use \GuzzleHttp\Client;
use \GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use \GuzzleHttp\Exception\RequestException;

use \Bitsbybit\Jira\Session\Exception as JiraException;
use Psr\Http\Message\ResponseInterface;

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
     * @var string
     */
    protected $tokenName;

    /**
     * @var string
     */
    protected $token;

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
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var CookieJarInterface
     */
    protected $jar;

    /**
     * Helper static method to create with GuzzleHttp\Client
     *
     * @param $domain
     * @param array $options
     * @return \Bitsbybit\Jira\Session
     */
    public static function create($domain, $options=[])
    {
        $httpClient = new Client([
            'base_uri' => 'https://billtrust.atlassian.net'
        ]);
        return new static($httpClient, $domain, $options);
    }

    /**
     *
     * @param ClientInterface $httpClient
     * @param string $domain
     * @param array $options
     */
    public function __construct(ClientInterface $httpClient, $domain, $options=[])
    {
        $this->client = $httpClient;
        $this->domain = $domain;
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
     * @param $key
     * @param $cookie
     * @param $var
     */
    public function parseCookie($key, $cookie, $var)
    {
        $parts = explode(';',$cookie);
        foreach($parts as $part){
            if( strpos($part, $key) !== false ){
                $itemParts = explode('=', $part);
                if( !empty($itemParts[1])) {
                    $this->{$var} = $itemParts[1];
                    break;
                }
            }
        }
    }
    /**
     * @param ResponseInterface $response
     */
    public function parseLoginResponse(ResponseInterface $response)
    {
//        EXPECTED HEADERS
//        [Set-Cookie] => Array
//        (
//            [0] => atlassian.xsrf.token=BCSV-JET9-JCQS-W9MT|9aee488c42b289d1edd64843135cbd305327a3bb|lout; Path=/; Secure
//        [1] => JSESSIONID=3FBD313E7799C5F6991B9BA9A1FBA4A2; Path=/; Secure; HttpOnly
//        [2] => studio.crowd.tokenkey=""; Domain=.billtrust.atlassian.net; Expires=Thu, 01-Jan-1970 00:00:10 GMT; Path=/; Secure; HttpOnly
//        [3] => studio.crowd.tokenkey=9pYSEi6i6IO0Q0f7td0HcQ00; Domain=.billtrust.atlassian.net; Path=/; Secure; HttpOnly
//        [4] => studio.crowd.tokenkey=;Version=1;Secure; HttpOnly
//            )
//            EXPECTED RESPONSE
//            {
//                "session":{
//                    "name":"JSESSIONID",
//                },
//                "loginInfo":{
//                    "loginCount":2,
//                    "previousLoginTime":"2013-12-04T07:54:59.824+0000"
//                }
//            }
        $parsedBody = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
        $this->tokenName = $parsedBody['session']['name'];
        $this->loginInfo = $parsedBody['loginInfo'];
        $this->sessionName = "JSESSIONID";

        $cookies = $response->getHeader('Set-Cookie');
        foreach($cookies as $cookie){
            if( strpos($cookie, "JSESSIONID") !== false ){
                $this->parseCookie('JSESSIONID', $cookie, 'sessionId');
            } else if ( strpos($cookie, $this->tokenName) !== false){
                $this->parseCookie($this->tokenName, $cookie, 'token');
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
     * @throws \Bitsbybit\Jira\Session\Exception
     */
    public function login($username, $password)
    {
        $url = $this->getUrl(Urls::V1_SESSION);

        try{
            $res = $this->client->request('POST', $url, [
                'json' => [ 'username' => $username, 'password' => $password ]
            ]);
        } catch (RequestException $e){
            $this->session = [];
            //All login failures result in a RequestException
            throw new JiraException($e);
        }

        $this->parseLoginResponse($res);

        return true;
    }

    public function getIssue($issueKey)
    {
        //$url = $this->getUrl(Urls::V2_ISSUE) . "/" . $issueKey;

        $res = $this->client->request('GET', Urls::V2_ISSUE . "/" . $issueKey, [
            'headers' => [
                'cookie' => "{$this->sessionName}={$this->sessionId}",
                "Content-Type" => "application/json"
            ]
        ]);
        var_dump($res->getBody()->getContents());
    }
}