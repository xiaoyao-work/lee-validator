<?php
namespace Lee\Validator;

class Validator {

    private static $validator = '';

    /**
     * 定义参数校验规则并进行处理
     *
     * @param   array   $data       参数数组 (外层请使用变量作为参数，而不是方法或final数组)
     * @param   array   $rules      参数校验规则
     * @param   array   $messages   自定义文案
     */
    public static function make(array $data, array $rules, array $messages = []) {
        self::lang($messages);
        self::$validator = new Core\Validator($data, $rules, $messages);
        self::dissectRuleStr();
        self::check();
        self::toType();
        self::toAlias();
    }

    /**
     * 注册语言
     * @param  string | array $lang 语言标识，或者语言翻译
     */
    public static function lang($lang) {
        Core\Lang::registerLang($lang);
    }

    public static function getHandler() {
        return self::$validator;
    }

    public static function __callStatic($name, $arguments) {
        call_user_func_array([self::$validator, $name], $arguments);
    }
}
