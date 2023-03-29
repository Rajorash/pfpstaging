<?php

namespace Olssonm\Zxcvbn;

use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;
use Illuminate\Support\ServiceProvider;
use Validator;

class ZxcvbnServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Extend the Laravel Validator with the "zxcvbn_min" rule
         */
        Validator::extend('zxcvbn_min', function($attribute, $value, $parameters) {
            $zxcvbn = new ZxcvbnPhp();
            $zxcvbn = $zxcvbn->passwordStrength($value);
            $target = 5;

            if (isset($parameters[0])) {
                $target = $parameters[0];
            }

            return ($zxcvbn['score'] >= $target);
        }, 'Your :attribute is not secure enough.');

        Validator::replacer('zxcvbn_min', function($message, $attribute) {
            $message = str_replace(':attribute', $attribute, $message);
            return $message;
        });

        /**
         * Extend the Laravel Validator with the "zxcvbn_dictionary" rule
         */
        Validator::extend('zxcvbn_dictionary', function($attribute, $value, $parameters) {
            $email = null;
            $username = null;

            if (isset($parameters[0])) {
                $email = $parameters[0];
                $username = $parameters[1];
            }

            $zxcvbn = new ZxcvbnPhp();
            $zxcvbn = $zxcvbn->passwordStrength($value, [$username, $email]);

            if (isset($zxcvbn['sequence'][0])) {
                $dictionary = $zxcvbn['sequence'][0];
                if (isset($dictionary->dictionaryName)) {
                    return false;
                }
            }

            return true;

        }, 'Your :attribute is insecure. It either matches a commonly used password, or you have used a similar username/password combination.');

        Validator::replacer('zxcvbn_dictionary', function($message, $attribute) {
            $message = str_replace(':attribute', $attribute, $message);
            return $message;
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('zxcvbn', function() {
            return new ZxcvbnPhp();
        });
    }
}
