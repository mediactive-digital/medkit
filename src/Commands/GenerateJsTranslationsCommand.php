<?php

namespace MediactiveDigital\MedKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

use Sepia\PoParser\Parser;
use MediactiveDigital\MedKit\Helpers\TranslationHelper;

class GenerateJsTranslationsCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medkit:generate-js-translations {path=public/js/translations} {--locale=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les fichiers JS qui permettent d\'avoir accès aux traductions';

    /**
     * Par ordre de prioritée
     *
     * @var array
     */
    protected $domains = [
            // 'messages' ,
//        'front',
//        'back',
    ];

    public function __construct(Filesystem $files) {

        parent::__construct();

        $this->domains = array_filter([config('laravel-gettext.domain')] + array_keys(config('laravel-gettext.source-paths')), 'is_string');

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info('Generating JS translations');

        $path = $this->argument('path');
        $defaultLocales = config('laravel-gettext.supported-locales');

        // Le ou les locales (si plusieurs locales, séparées par des virgules)
        $locales = $this->option('locale');
        $locales = $locales ? explode(',', $locales) : $defaultLocales;

        foreach ($locales as $locale) {

            if (in_array($locale, $defaultLocales)) {
                foreach ( $this->domains as $domaine) {

                    $translations = [];
                    $file = lang_path() . '/i18n/' . $locale . '/LC_MESSAGES/' . $domaine . '.po';
                    $file = file_exists($file) ? Parser::parseFile($file)->getEntries() : [];

                    foreach ($file as $value) {

                        if (!$value->isObsolete()) {

                            $msgId = $value->getMsgId();
                            $msgIdPlural = $value->getMsgIdPlural();

                            if ($msgIdPlural === null) {

                                $translations[$msgId] = $value->getMsgStr();
                            } else {

                                $msg = $msgPlural = '';
                                $plurals = $value->getMsgStrPlurals();

                                if ($plurals) {

                                    $msg = $plurals[0];
                                    $msgPlural = isset($plurals[1]) ? $plurals[1] : '';
                                }

                                $translations[$msgId] = $msg;
                                $translations[$msgIdPlural] = $msgPlural;
                            }
                        }
                    }
                }
                $translations = $translations ? json_encode($translations) : '{}';
                $dataTableTranslations = ($dataTableTranslations = TranslationHelper::getDataTable($locale)) ? json_encode($dataTableTranslations) : '{}';

                $script = <<<EOT
(function(root, factory) {
    if (typeof define === "function" && define.amd) define([], factory);
    else if (typeof exports === "object") module.exports = factory();
    else root.Lang = factory()
})(this, function() {
    var Lang = function() {
        this.locale = "$locale";
        this.messages = $translations;
        this.dt = $dataTableTranslations
    };
    Lang.prototype.getLocale = function() {
        return this.locale
    };
    Lang.prototype.getDataTable = function() {
        return this.dt
    };
    Lang.prototype._i = function(message, parameters) {
        parameters = typeof parameters !== "undefined" ? parameters : [];
        return this.messages[message] ? vsprintf(this.messages[message], parameters) : vsprintf(message, parameters)
    };
    Lang.prototype._n = function(singular, plural, n, parameters) {
        parameters = typeof parameters !== "undefined" ? parameters : [];
        if (n > 1) return this.messages[plural] ? vsprintf(this.messages[plural], parameters) : vsprintf(plural, parameters);
        return this.messages[singular] ? vsprintf(this.messages[singular], parameters) : vsprintf(singular, parameters)
    };
    var LangObject = new Lang;
    return LangObject
});
EOT;

                $localePath = rtrim($path, '/') . '/' . $locale . '.js';
                $this->makeDirectory($localePath);
                $this->files->put($localePath, $script);
            }
        }
    }

    /**
     * 
     * @param type $path
     * @return type
     */
    protected function makeDirectory($path) {

        if (!$this->files->isDirectory(dirname($path))) {

            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
