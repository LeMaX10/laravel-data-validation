<?php namespace LeMaX10\DataValidation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use LeMaX10\DataValidation\Rules\InnRule;
use LeMaX10\DataValidation\Rules\SnilsRule;

class DataValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('inn', InnRule::class);
        Validator::extend('snils', SnilsRule::class);
    }
}