<?php
namespace Bitsbybit\Jira\Http;

class Client
{
    public function request($method, $url, $options=[])
    {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $headers = ['Content-Type: application/json'];

        switch($method){
            case 'POST':
            case 'PUT':
                $jsonString = json_encode($options['json']);
                $headers[] = 'Content-Length: ' . strlen($jsonString);
                curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($c, CURLOPT_POSTFIELDS, $jsonString);
                break;
            case 'DELETE':
                curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
            curl_setopt($c, CURLOPT_COOKIE, $cookie);
        }

        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($c);
        if( $response === false ){
            throw new \Exception(curl_error($c), curl_errno($c));
        }

        return new Response($c, $response);
    }
}