<?php
namespace Bitsbybit\Jira\Session;

use GuzzleHttp\Exception\RequestException;

class Exception extends \Exception
{
    /**
     * Exception constructor.
     * @param RequestException $e
     */
    public function __construct(RequestException $e)
    {
        $code = $e->getCode();
        switch($code){
            case 401:
            case 403:
                $obj = json_decode($e->getResponse()->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
                $message = $obj['errorMessages'][0];
                break;
            default:
                $code = 500;
                $message = "Communication Failure";
                break;
        }

        parent::__construct($message, $code);
    }
}