<?php namespace LeMaX10\DataValidation\Exceptions;


use Throwable;

class ValidationErrorException extends \Exception
{
    public function __construct($message = "") {
        parent::__construct($message, 422);
    }
}