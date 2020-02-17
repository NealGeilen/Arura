<?php
namespace Arura\Exceptions;

use Exception;

class NotFound extends Exception{
    protected $message = "NotFound";
    protected $code = 404;
}
