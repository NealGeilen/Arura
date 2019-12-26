<?php
namespace Arura\Exceptions;

class Unauthorized extends \Exception{

    protected $message = "Unauthorized";
    protected $code = 401;
}