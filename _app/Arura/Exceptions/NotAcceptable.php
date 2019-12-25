<?php
namespace Arura\Exceptions;

class NotAcceptable extends \Exception{
    protected $message = "NotAcceptable";
    protected $code = 406;
}
