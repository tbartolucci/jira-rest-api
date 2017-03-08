<?php
namespace Bitsbybit\Jira\Http;

class Client
{
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
        if( isset($options['cookies']) ){
            $cookie = '';
            foreach($options['cookies'] as $key => $value){
                $cookie .= $key.'='.$value.'; ';
            }
            // Set a COOKIE in the request
            curl_setopt($c, CURLOPT_COOKIE, $cookie);
        }

        // Set the headers for the request
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
        $response = curl_exec($c);
        if( $response === false ){
            throw new \Exception(curl_error($c), curl_errno($c));
        }

        return new Response($c, $response);
    }
}