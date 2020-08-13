<?php declare(strict_types=1);
namespace LeMaX10\DataValidation\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Class SnilsRule
 *
 * @package LeMaX10\DataValidation\Rules
 */
class SnilsRule implements Rule
{
    /**
     * @var string
     */
    protected $messageCode;

    /**
     * @var int
     */
    protected $length = 11;

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validate(string $attribute, $value): bool
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
    public function passes(string $attribute, $value): bool
    {
        try {
            $snilsString = trim($value);
            if(empty($snilsString)) {
                throw new Exception('empty');
            }

            if (preg_match('/[^0-9]/', $snilsString)) {
                throw new Exception('onlyDigits');
            }

            if(Str::length($snilsString) !== $this->length) {
                throw new Exception('snilsLength');
            }

            if(!$this->checkDigit($snilsString)) {
                throw new Exception('snils');
            }

            return true;
        } catch (Exception $e) {
            $this->messageCode = $e->getMessage();
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('DataValidation::validation.snils.'. $this->messageCode);
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
    protected function checkDigit(string $value): bool
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
     * @return int
     */
    protected function getControlDigit(string $value): int
    {
        return (int) Str::substr($value, -2);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'snils';
    }
}