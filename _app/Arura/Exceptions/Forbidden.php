<?php
namespace Arura\Exceptions;

class Forbidden extends \Exception{

    protected $message = 'Fobidden';
    protected $code = 403;
}