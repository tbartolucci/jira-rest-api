<?php
namespace Bitsbybit\Jira\Http;

class Response
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $response;

    /**
     * Response constructor.
     * @param Client $client
     * @param string $response
     */
    public function __construct($client, $response)
    {
        $this->client = $client;
        $this->response = $response;
    }

    /**
     * 
     * @return mixed
     */
    public function getCode()
    {
        return $this->client->getResponseCode();
    }
    
    /**
     * @return string
     */
    public function getBody()
    {
        return substr($this->response, $this->client->getResponseHeaderSize(), strlen($this->response));
    }

    /**
     * @param bool $asArray
     * @return mixed
     */
    public function getJsonBody($asArray=true)
    {
        if( $asArray ){
            $opt = JSON_OBJECT_AS_ARRAY;
        }else{
            $opt = JSON_FORCE_OBJECT;
        }
        return json_decode($this->getBody(),$opt);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];

        $header_text = substr($this->response, 0, $this->client->getResponseHeaderSize());
        $lines = explode(PHP_EOL, $header_text);
        
        foreach ( $lines as $i => $line){
            if( empty($line) ) continue; 
            
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key][] = $value;
            }
        }

        return $headers;
    }

}