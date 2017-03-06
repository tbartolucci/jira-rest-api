<?php
namespace Bitsbybit\Jira\Session;

use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\RequestException;

use \Bitsbybit\Jira\Urls;
use \Bitsbybit\Jira\Session\Exception;

class Session
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
     * @var ClientInterface
     */
    private $client;

    /**
     *
     * @param ClientInterface $httpClient
     * @param string $domain
     * @param boolean $ssl
     */
    public function __construct(ClientInterface $httpClient, $domain, $ssl=true)
    {
        $this->domain = $domain;
        $this->ssl = $ssl;
        $this->client = $httpClient;
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
     * @param string $username
     * @param string $password
     * @return Session
     *
     * @throws Exception
     */
    public function login($username, $password)
    {
        $url = $this->getUrl(Urls::V1_SESSION);

        try{
            $res = $this->client->request('POST', $url, [
                'json' => [ 'username' => $username, 'password' => $password ]
            ]);
        } catch (RequestException $e){
            throw new Exception($e);
        }
        print_r(json_decode($res->getBody()));

        return $this;
    }
}