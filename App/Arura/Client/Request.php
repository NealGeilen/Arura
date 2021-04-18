<?php
namespace Arura\Client;

use Exception;

class Request{
    protected static function isXmlHttpRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * @param callable $callable
     */
    public static function handleXmlHttpRequest(callable $callable, $type = "POST"){
        if (self::isXmlHttpRequest() && is_callable($callable)){
            $request = new RequestHandler();
            $request->TriggerEvent();
            $request->sandbox($callable, $type);
        }
    }

}