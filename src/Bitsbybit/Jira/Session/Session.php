<?php
namespace Bitsbybit\Jira\Session;

use \GuzzleHttp\Client;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\RequestException;

use \Bitsbybit\Jira\Urls;
use \Bitsbybit\Jira\Session\Exception as JiraException;

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
     * @var ClientInterface
     */
    protected $client;

    /**
     * Helper static method to create with GuzzleHttp\Client
     *
     * @param $domain
     * @param array $options
     * @return \Bitsbybit\Jira\Session\Session
     */
    public static function create($domain, $options=[])
    {
        $httpClient = new Client();
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
     * Login to Jira Cloud using a username and password.
     *
     * @param string $username
     * @param string $password
     * @return boolean
     *
     * @throws Exception
     */
    public function login($username, $password)
    {
        $url = $this->getUrl(Urls::V1_SESSION);

        try{
//            EXPECTED RESPONSE
//            {
//                "session":{
//                    "name":"JSESSIONID",
//                    "value":"6E3487971234567896704A9EB4AE501F"
//                },
//                "loginInfo":{
//                    "failedLoginCount":1,
//                    "loginCount":2,
//                    "lastFailedLoginTime":"2013-11-27T09:43:28.839+0000",
//                    "previousLoginTime":"2013-12-04T07:54:59.824+0000"
//                }
//            }
            $res = $this->client->request('POST', $url, [
                'json' => [ 'username' => $username, 'password' => $password ]
            ]);
        } catch (RequestException $e){
            $this->session = [];
            //All login failures result in a RequestException
            throw new JiraException($e);
        }

        $this->session = json_decode($res->getBody(), JSON_OBJECT_AS_ARRAY);

        return true;
    }
}