<?php
namespace Arura\Exceptions;

use Exception;

class MethodNotAllowed extends Exception{

    protected $message = "Method Not Allowed";
    protected $code = 405;
}