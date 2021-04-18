<?php
namespace Arura\Exceptions;

use Exception;

class Unauthorized extends Exception{

    protected $message = "Unauthorized";
    protected $code = 401;
}