<?php

namespace MediactiveDigital\MedKit\Providers;

use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

use MediactiveDigital\MedKit\Translations\Translator;

use LaravelGettext;

class TranslationServiceProvider extends IlluminateTranslationServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        $this->app->singleton('translator', function($app) {

            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = LaravelGettext::getLocale();

            $trans = new Translator($loader, $locale);

            $trans->setFallback(config('laravel-gettext.fallback-locale'));

            return $trans;
        });
    }
}
