<?php namespace LeMaX10\DataValidation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use LeMaX10\DataValidation\Rules\InnRule;
use LeMaX10\DataValidation\Rules\SnilsRule;

class DataValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ .'/lang/', 'DataValidation');

        Validator::extend('inn', InnRule::class);
        Validator::extend('snils', SnilsRule::class);
    }
}