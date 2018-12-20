<?php

namespace Lee\Validator\Core;

class Lang {
    private static $default = 'zh-cn';
    private static $lang    = [];

    public static function lang($rule) {
        if (self::$lang == []) {
            self::initLang(self::$default);
        }
        return self::$lang[$rule];
    }

    public static function registerLang($lang) {
        self::initLang($lang);
    }

    private static function initLang($lang) {
        $lang_path = dirname(__DIR__) . '/Lang/';
        $default_lang = require($lang_path . strtolower(self::$default) . '.php');
        if (is_array($lang)) {
            self::$lang = array_merge($default_lang, $lang);
            return ;
        }
        $lang_file = $lang_path . strtolower($lang) . '.php';
        self::$lang = file_exists($lang_file) ? array_merge($default_lang, require_once($lang_file)) : $default_lang;
    }
}
