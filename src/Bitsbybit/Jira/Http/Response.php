<?php
namespace Bitsbybit\Jira\Http;

class Response
{
    /**
     * @var resource
     */
    private $ch;

    /**
     * @var string
     */
    private $response;

    /**
     * Response constructor.
     * @param resource $ch
     * @param string $response
     */
    public function __construct($ch, $response)
    {
        $this->ch = $ch;
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
    }

    /**
     * @return mixed
     */
    public function getHeaderSize()
    {
        return curl_getinfo($this->ch,CURLINFO_HEADER_SIZE);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return substr($this->getHeaderSize(),strlen($this->response));
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
        $headers = array();

        $header_text = substr($this->response, 0, $this->getHeaderSize());

        foreach (explode("\r\n", $header_text) as $i => $line){
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

}