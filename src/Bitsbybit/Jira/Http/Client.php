<?php
namespace Bitsbybit\Jira\Http;

class Client
{
    /**
     * 
     * @var string
     */
    private $cookieFile = __DIR__ . '/cookie.txt';
    
    /**
     * 
     * @param unknown $cookieFile
     */
    public function __construct($cookieFile)
    {
        $this->cookieFile = $cookieFile;
    }
    
    /**
     * 
     * @param unknown $method
     * @param unknown $url
     * @param array $options
     * @throws \Exception
     * @return \Bitsbybit\Jira\Http\Response
     */
    public function request($method, $url, $options=[])
    {
        $c = curl_init($url);
        
        // Return the contents of the response
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        // Return the response headers
        curl_setopt($c, CURLOPT_HEADER, 1);
        $headers = ['Content-Type: application/json'];

        switch($method){
            case 'POST':
            case 'PUT':
                $jsonString = json_encode($options['json']);
                $headers[] = 'Content-Length: ' . strlen($jsonString);
                // Set the request data in the body
                curl_setopt($c, CURLOPT_POSTFIELDS, $jsonString);
            case 'DELETE':
                // Set the request method to PUT, POST, or DELETE
                curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
                break;
            case 'GET':
            default:

                break;
        }
        
        curl_setopt( $c, CURLOPT_COOKIEJAR, $this->cookieFile );
        curl_setopt( $c, CURLOPT_COOKIEFILE, $this->cookieFile);
        
        // Set the headers for the request
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        
        // Verify SSL info if a CA file is available.
        if( isset($options['ca-file']) && file_exists($options['ca-file']) ){
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($c, CURLOPT_CAINFO, $options['ca-file']);
        }else{
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // Execute the request
        $response = curl_exec($c);
        if( $response === false ){
            throw new \Exception(curl_error($c), curl_errno($c));
        }

        return new Response($c, $response);
    }
}