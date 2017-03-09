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
     * @var string
     */
    private $caFile;
    
    /**
     * 
     * @var array
     */
    private $requestOptions;
    
    /**
     * 
     * @param array $options
     */
    public function __construct($options)
    {
        $this->cookieFile = $options['cookie-file'];
        if( isset($options['ca-file']) && file_exists($options['ca-file']) ){
            $this->caFile = $options['ca-file'];
        }
    }
    
    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
    }
    
    /**
     * @return mixed
     */
    public function getResponseHeaderSize()
    {
        return curl_getinfo($this->ch,CURLINFO_HEADER_SIZE);
    }
    
    /**
     * These options will be used for all requests
     */
    protected function setDefaultRequestOptions()
    {
        // Return the contents of the response
        $this->requestOptions[CURLOPT_RETURNTRANSFER] = 1;
        // Return the response headers
        $this->requestOptions[CURLOPT_HEADER] = 1;
        
        // Set local cookie storage
        $this->requestOptions[CURLOPT_COOKIEJAR] = $this->cookieFile;
        $this->requestOptions[CURLOPT_COOKIEFILE] = $this->cookieFile;
        
        // Verify SSL info if a CA file is available.
        if( $this->caFile !== null ){
            $this->requestOptions[CURLOPT_SSL_VERIFYPEER] = 1;
            $this->requestOptions[CURLOPT_CAINFO] = $this->caFile;
        }else{
            $this->requestOptions[CURLOPT_SSL_VERIFYPEER] = 0;
            $this->requestOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }
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
        $this->ch = curl_init();
        // Set the URL
        $this->requestOptions[CURLOPT_URL] = $url;
               
        $this->setDefaultRequestOptions();
        
        $headers = ['Content-Type: application/json'];

        switch($method){
            case 'POST':
            case 'PUT':
                $jsonString = json_encode($options['json']);
                $headers[] = 'Content-Length: ' . strlen($jsonString);
                // Set the request data in the body
                $this->requestOptions[CURLOPT_POSTFIELDS] = $jsonString;
            case 'DELETE':
                // Set the request method to PUT, POST, or DELETE
                $this->requestOptions[CURLOPT_CUSTOMREQUEST] = $method;
                break;
            case 'GET':
            default:
                unset($this->requestOptions[CURLOPT_CUSTOMREQUEST]);
                unset($this->requestOptions[CURLOPT_POSTFIELDS]);
                break;
        }
        
        // Set the headers for the request
        $this->requestOptions[CURLOPT_HTTPHEADER] = $headers;
        
        curl_setopt_array($this->ch,$this->requestOptions);

        // Execute the request
        $response = curl_exec($this->ch);
        if( $response === false ){
            throw new \Exception(curl_error($this->ch), curl_errno($this->ch));
        }

        return new Response($this, $response);
    }
}