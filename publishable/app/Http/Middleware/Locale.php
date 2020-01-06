<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use Carbon\Carbon;

use Closure;
use LaravelGettext;

class Locale {

    /**
     * Doit rediriger les pages sans prefix de lang sur le prefix, en fonction de la locale du user
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {

        // Note Medkit : Exemple de gestion des locales pour une application localisée

        $get = $request->method() === 'GET';
        $locale = $localeUrl = $get ? $request->segment(1) : '';
        $supportedLocales = config('laravel-gettext.supported-locales');

        // Si aucune locale ou non supportée
        if (!$locale || !in_array($locale, $supportedLocales)) {

            // On récupère la locale actuelle
            $locale = LaravelGettext::getLocale();

            $defaultLocale = config('laravel-gettext.fallback-locale');

            /**
             * Si la locale actuelle est la même que celle par défaut, c'est potentiellement dû au fallback.
             * On se base plutôt sur la langue du navigateur si possible.
             */
            if ($locale == $defaultLocale) {

                // On vérifie la langue du navigateur
                $requestLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2); // i.e. "de"

                // On vérifie qu'elle existe et qu'elle est supportée
                if ($requestLocale && $requestLocale != $defaultLocale && in_array($requestLocale, $supportedLocales)) {

                    $locale = $requestLocale;
                }
            }
        }

        // Set locale and refresh locale file
        LaravelGettext::getTranslator()->setLocale($locale);
        Carbon::setLocale($locale);

        // Si il n'y avait rien dans l'url (GET), on redirige sur la home localisée
        if ($get && !$localeUrl) {

            // Création des alternates pour robots

            $redirectHeaderLinks = [];

            foreach ($supportedLocales as $supportedLocale) {

                $redirectHeaderLinks[] = '</' . $supportedLocale . '>; rel="alternate"; hreflang="' . $supportedLocale . '"';
            }

            $strHeaderLink = '</fr>; rel="alternate"; hreflang="x-default", ' . implode(', ', $redirectHeaderLinks);
        
            return redirect()->route('home', [
                    'lang' => $locale
                ], 302)
                ->header('Accept-Language', 'x-default')
                ->header('Link', $strHeaderLink);
        }

        return $next($request);
    }
}
