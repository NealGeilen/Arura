<?php
namespace Arura\Exceptions;

use Exception;

class BadRequest extends Exception{

    protected $message = "Bad Request";
    protected $code = 400;
}