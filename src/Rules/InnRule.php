<?php namespace LeMaX10\DataValidation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use LeMaX10\Exceptions\ValidationErrorException;

/**
 * Class InnRule
 *
 * @package LeMaX10\DataValidation\Rules
 */
class InnRule implements Rule
{
    /**
     * @var
     */
    protected $messageCode;

    /**
     * @var array
     */
    protected $length = [10, 12];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $innString = trim($value);
            if (empty($innString)) {
                throw new ValidationErrorException('empty');
            }

            if (preg_match('/[^0-9]/', $innString)) {
                throw new ValidationErrorException('onlyDigits');
            }

            $length = Str::length($innString);
            if (!in_array($length, $this->length, true)) {
                throw new ValidationErrorException('length');
            }

            if(($length === 10 && !$this->validateTenDigitInn($innString)) || !$this->validateOtherInn($innString)) {
                throw new ValidationErrorException('controlDigit');
            }

            return true;
        } catch(ValidationErrorException $e) {
            $this->messageCode = $e->getMessage();
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('validation.data-validation.'. $this->messageCode);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function validateTenDigitInn(string $value)
    {
        $coefficients = [2, 4, 10, 3, 5, 9, 4, 6, 8];
        $controlDigit = (int) $value{9};

        return $this->checkDigits($value, $coefficients) === $controlDigit;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function validateOtherInn(string $value)
    {
        $coefficientFirst = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
        $coefficientSecond = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

        $controlDigitFirst = (int) $value{10};
        $controlDigitSecond = (int) $value{11};

        return $this->checkDigits($value, $coefficientFirst) === $controlDigitFirst
            && $this->checkDigits($value, $coefficientSecond)=== $controlDigitSecond;
    }

    /**
     * @param string $value
     * @param array  $coeffs
     *
     * @return int
     */
    protected function checkDigits(string $value, array $coeffs)
    {
        $result = 0;
        foreach ($coeffs as $key => $i) {
            $digit = (int) $value{$i};
            $result += $key * $digit;
        }

        return $result % 11 % 10;
    }
}