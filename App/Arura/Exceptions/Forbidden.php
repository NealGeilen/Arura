<?php
namespace Arura\Exceptions;

use Exception;

class Forbidden extends Exception{

    protected $message = 'Forbidden';
    protected $code = 403;
}