<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\sms;

defined('_EXEC') or die();

class Plivo
{

    private $AUTH_ID = "MAN2E4YZLDFFHM2RKYTK5ZGFD";

    private $AUTH_TOKEN = "ZjU5MGY2ZGMzZSUHG978F97YGDYTdhMTkwZjYzM2JkMTQ1";

    private $response = "";

    function __construct($auth_id, $auth_token)
    {
        self::setAUTHId($auth_id);
        self::setAUTHToken($auth_token);
    }

    public function getResponseHttpStatus()
    {
        $part = "";
        $code = "";
        $status = "";

        if (strpos($this->getResponse(), "HTTP/") != - 1) {

            $part = substr($this->getResponse(), strpos($this->getResponse(), "HTTP/") + 5);

            $part = substr($part, strpos($part, " ") + 1);

            $code = substr($part, 0, strpos($part, " "));

            $status = substr($part, strpos($part, " ") + 1);

            $status = substr($status, 0, strpos($status, "Content-Type:") - 1);
        }

        return array(
            "code" => (int) $code,
            "status" => $status
        );
    }

    public function getResponse()
    {
        return $this->result;
    }

    public function setResponse($result)
    {
        $this->result = $result;
        return $this;
    }

    public function getBalance()
    {
        $result = 0;

        return $result;
    }

    public function sendSMS($text, $arrayTel)
    {
        $result = array();
        $response = "";

        $url = 'https://api.plivo.com/v1/Account/' . self::getAUTHId() . '/Message/';

        foreach ($arrayTel as $key => $tel) {

            $data = array(
                "src" => "SM123",
                "dst" => $tel,
                "text" => $text
            );
            $data_string = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_USERPWD, self::getAUTHId() . ":" . self::getAUTHToken());
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            $this->setResponse($response);

            $status = $this->getResponseHttpStatus();

            $result[] = array(
                "dst" => $tel,
                "status" => $status['status']
            );
        }

        return $result;
    }

    public function getAUTHId()
    {
        return $this->AUTH_ID;
    }

    public function setAUTHId($AUTH_ID)
    {
        $this->AUTH_ID = $AUTH_ID;
        return $this;
    }

    public function getAUTHToken()
    {
        return $this->AUTH_TOKEN;
    }

    public function setAUTHToken($AUTH_TOKEN)
    {
        $this->AUTH_TOKEN = $AUTH_TOKEN;
        return $this;
    }
}

?>