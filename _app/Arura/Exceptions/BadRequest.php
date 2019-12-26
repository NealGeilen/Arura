<?php
namespace Arura\Exceptions;

class BadRequest extends \Exception{

    protected $message = "Bad Request";
    protected $code = 400;
}