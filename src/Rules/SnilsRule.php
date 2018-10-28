<?php namespace LeMaX10\DataValidation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use LeMaX10\DataValidation\Exceptions\ValidationErrorException;

/**
 * Class SnilsRule
 *
 * @package LeMaX10\DataValidation\Rules
 */
class SnilsRule implements Rule
{
    /**
     * @var
     */
    protected $messageCode;

    /**
     * @var array
     */
    protected $length = 11;

    public function validate($attribute, $value)
    {
        return $this->passes($attribute, $value);
    }

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
            $snilsString = trim($value);
            if(empty($snilsString)) {
                throw new ValidationErrorException('empty');
            }

            if (preg_match('/[^0-9]/', $snilsString)) {
                throw new ValidationErrorException('onlyDigits');
            }

            if(Str::length($snilsString) !== $this->length) {
                throw new ValidationErrorException('snilsLength');
            }

            if(!$this->checkDigit($snilsString)) {
                throw new ValidationErrorException('snils');
            }

            return true;
        } catch (ValidationErrorException $e) {
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
        return Lang::get('validation.'. $this->messageCode);
    }

    /**
     * @param string $value
     *
     * @return float|int
     */
    protected function calcSum(string $value)
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $value{$i} * (9 - $i);
        }

        return $sum;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function checkDigit(string $value)
    {
        $sum = $this->calcSum($value);

        $digit = $sum < 100 ? $sum : $sum % 101;
        if ($digit === 100) {
            $digit = 0;
        }

        return $digit === $this->getControlDigit($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function getControlDigit(string $value)
    {
        return (int) Str::substr($value, -2);
    }
}