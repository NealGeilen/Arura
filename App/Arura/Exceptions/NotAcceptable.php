<?php
namespace Arura\Exceptions;

use Exception;

class NotAcceptable extends Exception{
    protected $message = "NotAcceptable";
    protected $code = 406;
}
