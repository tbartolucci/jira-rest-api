<?php
namespace Bitsbybit\Jira;

use Bitsbybit\Jira\Http\Response as HttpResponse;

class SessionException extends \Exception
{
    public function __construct(HttpResponse $res)
    {
        $code = $res->getCode();
        switch($code){
            case 401:
            case 403:
                $bodyContents = $res->getJsonBody();
                $message = $bodyContents['errorMessages'][0];
                break;
            default:
                $code = 500;
                $message = "Communication Failure with response: {$res->getBody()}";
                break;
        }
        parent::__construct($message, $code);
    }
}