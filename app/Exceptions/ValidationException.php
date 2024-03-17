<?php 

namespace App\Exceptions;

use App\Enums\StatusCode;
use App\Facades\Utils;
use Exception;
class ValidationException extends Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        $errors = json_decode($this->message, true);

        $formattedErrors = [];
        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $formattedErrors[] = $field . ': ' . $error;
            }
        }

        return Utils::setResponse(
            StatusCode::VALIDATION, 
            null, 
            $formattedErrors
        );
    }
}