<?php
namespace Arura\Exceptions;

class Error extends \Exception{

    protected $message = 'Failed';
    protected $code = 500;
}