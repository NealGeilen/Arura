<?php
namespace Arura\Exceptions;

use Exception;

class Error extends Exception{

    protected $message = 'Failed';
    protected $code = 500;
}