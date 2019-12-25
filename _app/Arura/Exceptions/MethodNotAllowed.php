<?php
namespace Arura\Exceptions;

class MethodNotAllowed extends \Exception{

    protected $message = "Method Not Allowed";
    protected $code = 405;
}