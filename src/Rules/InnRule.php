<?php declare(strict_types=1);
namespace LeMaX10\DataValidation\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Class InnRule
 *
 * @package LeMaX10\DataValidation\Rules
 */
class InnRule implements Rule
{
    /**
     * @var string
     */
    protected $messageCode;

    /**
     * @var array
     */
    protected $length = [10, 12];

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function validate($attribute, $value): bool
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
    public function passes($attribute, $value): bool
    {
        try {
            $innString = trim($value);
            if (empty($innString)) {
                throw new Exception('empty');
            }

            if (preg_match('/[^0-9]/', $innString)) {
                throw new Exception('onlyDigits');
            }

            if (!$this->validateValue($innString)) {
                throw new Exception('inn');
            }

            return true;
        } catch(Exception $e) {
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
        return trans('DataValidation::validation.inn.'. $this->messageCode);
    }

    /**
     * @param string $value
     * @return bool
     * @throws Exception
     */
    protected function validateValue(string $value): bool
    {
        $length = Str::length($value);
        if (!in_array($length, $this->length, true)) {
            throw new Exception('length');
        }

        if ($length === 10) {
            return $this->validateTenDigitInn($value);
        } else if ($length === 12) {
            return $this->validateTwelvyDigitInn($value);
        }

        return false;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function validateTenDigitInn(string $value): bool
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
    protected function validateTwelvyDigitInn(string $value): bool
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
    protected function checkDigits(string $value, array $coeffs): int
    {
        $result = 0;
        foreach ($coeffs as $key => $dig) {
            $digit = (int) $value{$key};
            $result += $dig * $digit;
        }

        return (int) $result % 11 % 10;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'inn';
    }
}
