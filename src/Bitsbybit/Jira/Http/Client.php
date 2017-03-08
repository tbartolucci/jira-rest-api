<?php
namespace Bitsbybit\Jira\Http;

class Client
{
    /**
     * @var resource
     */
    private $ch;
    /**
     * 
     * @var string
     */
    private $cookieFile;
    
    /**
     * 
     * @param string $cookieFile
     */
    public function __construct($cookieFile)
    {
        $this->cookieFile = $cookieFile;
        $this->ch = curl_init();
    }

    /**
     * @return resource
     */
    public function getClientResource()
    {
        return $this->ch;
    }
    
    /**
     * 
     * @param string $method
     * @param string $url
     * @param array $options
     * @throws \Exception
     * @return \Bitsbybit\Jira\Http\Response
     */
    public function request($method, $url, $options=[])
    {
        // Set the URL
        curl_setopt($this->ch, CURLOPT_URL, $url);
        // Return the contents of the response
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        // Return the response headers
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        $headers = ['Content-Type: application/json'];

        switch($method){
            case 'POST':
            case 'PUT':
                $jsonString = json_encode($options['json']);
                $headers[] = 'Content-Length: ' . strlen($jsonString);
                // Set the request data in the body
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonString);
            case 'DELETE':
                // Set the request method to PUT, POST, or DELETE
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
            case 'GET':
            default:

                break;
        }

        // Set local cookie storage
        curl_setopt( $this->ch, CURLOPT_COOKIEJAR, $this->cookieFile );
        curl_setopt( $this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        
        // Set the headers for the request
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        // Verify SSL info if a CA file is available.
        if( isset($options['ca-file']) && file_exists($options['ca-file']) ){
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($this->ch, CURLOPT_CAINFO, $options['ca-file']);
        }else{
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // Execute the request
        $response = curl_exec($this->ch);
        if( $response === false ){
            throw new \Exception(curl_error($this->ch), curl_errno($this->ch));
        }

        return new Response($this, $response);
    }
}