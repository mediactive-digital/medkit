<?php

namespace MediactiveDigital\MedKit\Helpers;

use \LaravelGettext;
use MedKit\Translations\MoFileLoader;

class TranslationHelper {

    public static function get() {

        $file = __DIR__ . '/../../resources/lang/i18n/' . LaravelGettext::getLocale() . '/LC_MESSAGES/messages.mo';
        $file = file_exists($file) ? (new MoFileLoader)->loadResource($file) : [];
        $file = $file ? json_encode($file) : '{}';

        return '<script type="text/javascript">
					(function(root, factory) {
					    if (typeof define === "function" && define.amd) define([], factory);
					    else if (typeof exports === "object") module.exports = factory();
					    else root.Lang = factory()
					})(this, function() {
					    var Lang = function() {
					        this.locale = "' . LaravelGettext::getLocaleLanguage() . '";
					        this.messages = ' . $file . '
					    };
					    Lang.prototype.getLocale = function() {
					        return this.locale
					    };
					    Lang.prototype._i = function(message, parameters) {
					        parameters = typeof parameters !== "undefined" ? parameters : [];
					        return this.messages[message] ? vsprintf(this.messages[message], parameters) : vsprintf(message, parameters)
					    };
					    Lang.prototype._n = function(singular, plural, n, parameters) {
					        parameters = typeof parameters !== "undefined" ? parameters : [];
					        if (n > 1) {
					            if (this.messages[plural]) {
					                var replace = "/^" + (this.messages[singular] ? this.messages[singular] : singular) + "|/";
					                var re = new RegExp(replace, "g");
					                return this.messages[plural] ? vsprintf(this.messages[plural].replace(re, ""), parameters) : vsprintf(plural, parameters)
					            }
					            return vsprintf(plural, parameters)
					        }
					        return this.messages[singular] ? vsprintf(this.messages[singular], parameters) : vsprintf(singular, parameters)
					    };
					    return Lang
					});
					(function() {
					    Lang = new Lang
					})();
				</script>';
    }
}