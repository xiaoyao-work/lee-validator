<?php

namespace Lee\Validator\Core;

class Validator {
    protected $data;
    protected $rules;
    protected $messages;
    protected $scale        = 8;
    protected $rules_data   = [];
    protected $alias        = [];
    protected $to_type      = [];
    protected $array_str    = [];
    protected $float        = [];
    protected $files        = [];
    protected $errors       = [];
    protected $fails        = false;
    protected $to_type_keys = [
        'string', 'boolean', 'integer', 'float', 'array', 'object',
    ];


    // check rule : about exists and not null
    protected $implicitRules = [
        'file_exists', 'file_type_in', 'file_max', 'file_min', 'file_size_between', 'present', 'required_with', 'required_with_all', 'required_without', 'required_without_all', 'same', 'different',
    ];
    // used on function : check_to_type 、toType
    protected $to_type_keys_mul = [
        'str_array', 'scale',
    ];

    public function __construct(array &$data, array $rules, array $messages) {
        $this->data     = $data;
        $this->rules    = $rules;
        $this->messages = $messages;
    }

    public function fails() {
        return $this->fails;
    }

    public function errors() {
        return $this->errors;
    }

    public function data() {
        return $this->data;
    }

    public function dissectRuleStr() {
        $dissect_obj = new DissectRule();
        foreach ($this->rules as $key => $rule) {
            $dissect_obj->handle($rule, $dissect_data);
            $this->rules_data[$key] = $dissect_data;
        }
    }

    public function check() {
        foreach ($this->rules_data as $attribute => $v_d) {
            foreach ($this->implicitRules as $key) {
                if (!isset($v_d[$key])) {
                    continue;
                }
                $rule_data = $v_d[$key];
                switch ($key) {
                case 'file_exists':$rule_result = $this->check_file_exists($attribute);
                    break;
                case 'file_type_in':$rule_result = $this->check_file_type_in($attribute, $rule_data);
                    break;
                case 'file_max':$rule_result = $this->check_file_max($attribute, $rule_data);
                    break;
                case 'file_min':$rule_result = $this->check_file_min($attribute, $rule_data);
                    break;
                case 'file_size_between':$rule_result = $this->check_file_size_between($attribute, $rule_data);
                    break;
                case 'present':$rule_result = $this->check_present($attribute);
                    break;
                case 'different':$rule_result = $this->check_different($attribute, $rule_data);
                    break;
                case 'same':$rule_result = $this->check_same($attribute, $rule_data);
                    break;
                case 'required_with':$rule_result = $this->check_required_with($attribute, $rule_data);
                    break;
                case 'required_with_all':$rule_result = $this->check_required_with_all($attribute, $rule_data);
                    break;
                case 'required_without':$rule_result = $this->check_required_without($attribute, $rule_data);
                    break;
                case 'required_without_all':$rule_result = $this->check_required_without_all($attribute, $rule_data);
                    break;
                default:
                    $rule_result = true;
                }
                if (!$rule_result) {
                    return false;
                }
            }

            // check other rule when it is exists!
            if (!array_key_exists($attribute, $this->data)) {
                continue;
            }

            $value = $this->data[$attribute];

            foreach ($v_d as $rule => $rule_data) {
                switch ($rule) {
                case 'alpha':$rule_result = $this->check_alpha($attribute, $value);
                    break;
                case 'num':$rule_result = $this->check_num($attribute, $value);
                    break;
                case 'alpha_num':$rule_result = $this->check_alpha_num($attribute, $value);
                    break;
                case 'alpha_dish':$rule_result = $this->check_alpha_dish($attribute, $value);
                    break;
                case 'var':$rule_result = $this->check_var($attribute, $value);
                    break;
                case 'ip':$rule_result = $this->check_ip($attribute, $value);
                    break;
                case 'url':$rule_result = $this->check_url($attribute, $value);
                    break;
                case 'email':$rule_result = $this->check_email($attribute, $value);
                    break;
                case 'mobile':$rule_result = $this->check_mobile($attribute, $value);
                    break;
                case 'json':$rule_result = $this->check_json($attribute, $value);
                    break;
                case 'timestamp':$rule_result = $this->check_timestamp($attribute, $value);
                    break;
                case 'date_format':$rule_result = $this->check_date_format($attribute, $value, $rule_data);
                    break;
                case 'regex':$rule_result = $this->check_regex($attribute, $value, $rule_data);
                    break;
                case 'string':$rule_result = $this->check_string($attribute, $value);
                    break;
                case 'boolean':$rule_result = $this->check_boolean($attribute, $value);
                    break;
                case 'integer':$rule_result = $this->check_integer($attribute, $value);
                    break;
                case 'float':$rule_result = $this->check_float($attribute, $value);
                    break;
                case 'array':$rule_result = $this->check_array($attribute, $value);
                    break;
                case 'object':$rule_result = $this->check_object($attribute, $value);
                    break;
                case 'object_of':$rule_result = $this->check_object_of($attribute, $value, $rule_data);
                    break;
                case 'integer_str':$rule_result = $this->check_integer_str($attribute, $value);
                    break;
                case 'float_str':$rule_result = $this->check_float_str($attribute, $value);
                    break;
                case 'numeric_str':$rule_result = $this->check_numeric_str($attribute, $value);
                    break;
                case 'array_str':$rule_result = $this->check_array_str($attribute, $value);
                    break;
                case 'max':$rule_result = $this->check_max($attribute, $value, $rule_data);
                    break;
                case 'length_max':$rule_result = $this->check_length_max($attribute, $value, $rule_data);
                    break;
                case 'min':$rule_result = $this->check_min($attribute, $value, $rule_data);
                    break;
                case 'length_min':$rule_result = $this->check_length_min($attribute, $value, $rule_data);
                    break;
                case 'length':$rule_result = $this->check_length($attribute, $value, $rule_data);
                    break;
                case 'between':$rule_result = $this->check_between($attribute, $value, $rule_data);
                    break;
                case 'length_between':$rule_result = $this->check_length_between($attribute, $value, $rule_data);
                    break;
                case 'in':$rule_result = $this->check_in($attribute, $value, $rule_data);
                    break;
                case 'not_in':$rule_result = $this->check_not_in($attribute, $value, $rule_data);
                    break;
                case 'filled':$rule_result = $this->check_filled($attribute, $value);
                    break;
                case 'distinct':$rule_result = $this->check_distinct($attribute, $value);
                    break;
                case 'alias':$rule_result = $this->add_alias($attribute, $rule_data);
                    break;
                case 'to_type':$rule_result = $this->check_to_type($attribute, $rule_data);
                    break;

                default:
                    $rule_result = true;
                }
                if (!$rule_result) {
                    return false;
                }
            }
        }
        return true;
    }

    public function toType() {
        if ($this->fails) {
            return false;
        }
        foreach ($this->to_type as $key => $type_data) {
            $value = array_key_exists($key, $this->data) ? $this->data[$key] : "";
            switch ($type_data) {
            case 'string':$this->data[$key] = strval($value);
                break;
            case 'boolean':$this->data[$key] = boolval($value);
                break;
            case 'integer':$this->data[$key] = intval($value);
                break;
            case 'float':$this->data[$key] = floatval($value);
                break;
            case 'array':$this->data[$key] = (array) $value;
                break;
            case 'object':$this->data[$key] = (object) $value;
                break;
            default:
                $div_arr = explode(':', $type_data);
                switch ($div_arr[0]) {
                case 'str_array':
                    if (!$this->toType_str_array($key, $value, $div_arr[1])) {
                        return false;
                    }
                    break;
                case 'scale':
                    if (!$this->toType_scale($key, $value, $div_arr[1])) {
                        return false;
                    }
                    break;
                default:
                    break;
                }
                break;
            }
        }
        return true;
    }

    public function toAlias() {
        if ($this->fails) {
            return false;
        }

        $data = [];
        if (!empty($this->alias)) {
            foreach ($this->data as $key => $value) {
                $new_key        = isset($this->alias[$key]) ? $this->alias[$key] : $key;
                $data[$new_key] = $value;
            }
            $this->data = $data;
        }
        return true;
    }

    protected function _check_not_null($attribute) {
        if (isset($this->data[$attribute]) && $this->data[$attribute] !== "" && $this->data[$attribute] !== []) {
            return true;
        }
        return false;
    }

    protected function getFile($attribute) {
        if (array_key_exists($attribute, $this->files)) {
            return $this->files[$attribute];
        }
        if (!isset($_FILES[$attribute])) {
            $this->files[$attribute] = [];
        } else {
            $this->files[$attribute] = $_FILES[$attribute];
            if ($this->files[$attribute]['error'] != 0) {
                throw new \Exception("Upload file failed");
            }
        }

        return $this->files[$attribute];
    }

    protected function set_error_message() {
        $params    = func_get_args();
        $rule      = $params[0];
        $attribute = $params[1];

        $this->fails = true;
        $custom_key  = $attribute . "." . $rule;
        if (array_key_exists($custom_key, $this->messages)) {
            $this->errors[$attribute] = $this->messages[$custom_key];
        } else {
            switch (func_num_args()) {
            case 2:
                $this->errors[$attribute] = str_replace('{attribute}', $attribute, Lang::lang($rule));
                break;
            case 3:
                $replace = [
                    '{attribute}' => $attribute,
                    '{param_1}'   => $params[2],
                ];
                $this->errors[$attribute] = strtr(Lang::lang($rule), $replace);
                break;
            case 4:
                $replace = [
                    '{attribute}' => $attribute,
                    '{param_1}'   => $params[2],
                    '{param_2}'   => $params[3],
                ];
                $this->errors[$attribute] = strtr(Lang::lang($rule), $replace);
                break;
            default:
                throw new \BadFunctionCallException("set_error_message 只允许2-4个参数");
                break;
            }
        }
    }

    protected function check_present($attribute) {
        if (!$this->_check_not_null($attribute)) {
            $this->set_error_message('present', $attribute);
            return false;
        }
        return true;
    }

    protected function check_alpha($attribute, $value) {
        $rv = preg_match("/^[a-zA-Z]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('alpha', $attribute);
            return false;
        }
        return true;
    }

    protected function check_num($attribute, $value) {
        $rv = preg_match("/^[0-9]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('num', $attribute);
            return false;
        }
        return true;
    }

    protected function check_alpha_num($attribute, $value) {
        $rv = preg_match("/^[0-9A-Za-z]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('alpha_num', $attribute);
            return false;
        }
        return true;
    }

    protected function check_alpha_dish($attribute, $value) {
        $rv = preg_match("/^[0-9A-Za-z_-]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('alpha_dish', $attribute);
            return false;
        }
        return true;
    }

    protected function check_var($attribute, $value) {
        $rv = preg_match("/^[A-Za-z_]{1}[0-9A-Za-z_]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('var', $attribute);
            return false;
        }
        return true;
    }

    protected function check_ip($attribute, $value) {
        if (!filter_var(strval($value), FILTER_VALIDATE_IP)) {
            $this->set_error_message('ip', $attribute);
            return false;
        }
        return true;
    }

    protected function check_url($attribute, $value) {
        if (!filter_var(strval($value), FILTER_VALIDATE_URL)) {
            $this->set_error_message('url', $attribute);
            return false;
        }
        return true;
    }

    protected function check_email($attribute, $value) {
        if (!filter_var(strval($value), FILTER_VALIDATE_EMAIL)) {
            $this->set_error_message('email', $attribute);
            return false;
        }
        return true;
    }

    protected function check_mobile($attribute, $value) {
        $rv = preg_match("/^1[3-8]{1}[0-9]{9}$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('mobile', $attribute);
            return false;
        }
        return true;
    }

    protected function check_json($attribute, $value) {
        if (json_decode(strval($value), true) == []) {
            $this->set_error_message('json', $attribute);
            return false;
        }
        return true;
    }

    protected function check_timestamp($attribute, $value) {
        $value = strval($value);
        $rv    = preg_match("/^[0-9]*$/", $value);
        if ($rv == 0) {
            $this->set_error_message('timestamp', $attribute);
            return false;
        }

        @$timestamp = date("Y-m-d H:i:s", $value);
        if ($timestamp == false) {
            $this->set_error_message('timestamp', $attribute);
            return false;
        }
        return true;
    }

    protected function check_date_format($attribute, $value, $rule_data) {
        $value      = strval($value);
        @$timestamp = strtotime($value);
        if ($timestamp == false) {
            $this->set_error_message('date_format', $attribute);
            return false;
        }

        @$date = date($rule_data, $timestamp);
        if ($date == false) {
            $this->set_error_message('date_format', $attribute);
            return false;
        }

        if ($date != $value) {
            $this->set_error_message('date_format', $attribute);
            return false;
        }
        return true;
    }

    protected function check_regex($attribute, $value, $rule_data) {
        $rv = preg_match($rule_data, strval($value));
        if ($rv == 0) {
            $this->set_error_message('regex', $attribute);
            return false;
        }
        return true;
    }

    protected function check_string($attribute, $value) {
        if (!is_string($value)) {
            $this->set_error_message('string', $attribute);
            return false;
        }
        return true;
    }

    protected function check_boolean($attribute, $value) {
        if (!is_bool($value)) {
            $this->set_error_message('boolean', $attribute);
            return false;
        }
        return true;
    }

    protected function check_integer($attribute, $value) {
        if (!is_int($value)) {
            $this->set_error_message('integer', $attribute);
            return false;
        }
        return true;
    }

    protected function check_float($attribute, $value) {
        if (!is_float($value)) {
            $this->set_error_message('float', $attribute);
            return false;
        }
        $this->float[$attribute] = $value;
        return true;
    }

    protected function check_array($attribute, $value) {
        if (!is_array($value)) {
            $this->set_error_message('array', $attribute);
            return false;
        }
        return true;
    }

    protected function check_object($attribute, $value) {
        if (!is_object($value)) {
            $this->set_error_message('object', $attribute);
            return false;
        }
        return true;
    }

    protected function check_object_of($attribute, $value, $rule_data) {
        if (!is_object($value)) {
            $this->set_error_message('object', $attribute);
            return false;
        }

        if (get_class($value) != $rule_data) {
            $this->set_error_message('object_of', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_integer_str($attribute, $value) {
        $rv = preg_match("/^[-]?[1-9]{1}[0-9]*$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('integer_str', $attribute);
            return false;
        }
        return true;
    }

    protected function check_float_str($attribute, $value) {
        $value = strval($value);
        $rv    = preg_match("/^[-]?(([1-9]{1}[0-9]*\.[0-9]*)|(0\.[0-9]+))$/", strval($value));
        if ($rv == 0) {
            $this->set_error_message('float_str', $attribute);
            return false;
        }
        $this->float[$attribute] = $value;
        return true;
    }

    protected function check_numeric_str($attribute, $value) {
        if (!is_numeric($value)) {
            $this->set_error_message('numeric_str', $attribute);
            return false;
        }
        return true;
    }

    protected function check_array_str($attribute, $value) {
        $value = strval($value);
        $len   = strlen($value);
        if ($len < 3) {
            $this->set_error_message('array_str', $attribute);
            return false;
        }
        if ($value[0] != "[" || $value[$len - 1] != "]") {
            $this->set_error_message('array_str', $attribute);
            return false;
        }
        $this->array_str[$attribute] = substr($value, 1, $len - 2);
        return true;
    }

    protected function check_max($attribute, $value, $rule_data) {
        $result = bcsub($rule_data, $value);
        $rv     = preg_match("/^-.*$/", $result);
        if ($rv == 1) {
            $this->set_error_message('max', $attribute);
            return false;
        }
        return true;
    }

    protected function check_length_max($attribute, $value, $rule_data) {
        $value = mb_strlen(strval($value));
        $len   = intval($rule_data);
        if ($value > $len) {
            $this->set_error_message('length_max', $attribute);
            return false;
        }
        return true;
    }

    protected function check_min($attribute, $value, $rule_data) {
        $result = bcsub($value, $rule_data);
        $rv     = preg_match("/^-.*$/", $result);
        if ($rv == 1) {
            $this->set_error_message('min', $attribute);
            return false;
        }
        return true;
    }

    protected function check_length_min($attribute, $value, $rule_data) {
        $value = mb_strlen(strval($value));
        $len   = intval($rule_data);
        if ($value < $len) {
            $this->set_error_message('length_min', $attribute);
            return false;
        }
        return true;
    }

    protected function check_length($attribute, $value, $rule_data) {
        $value = mb_strlen(strval($value));
        $len   = intval($rule_data);
        if ($len != $value) {
            $this->set_error_message('length', $attribute, $len);
            return false;
        }
        return true;
    }

    protected function check_between($attribute, $value, $rule_data) {
        $div_arr = explode(',', $rule_data);
        if (count($div_arr) != 2) {
            throw new \Exception("Illegal PHPValidator expression: The 'between' rule's value must like ' min , max '");
        }
        $v1 = trim($div_arr[0]);
        $v2 = trim($div_arr[1]);
        if ($v2 < $v1) {
            $v  = $v1;
            $v1 = $v2;
            $v2 = $v;
        }

        $max_result = bcsub($v2, $value);
        $rv         = preg_match("/^-.*$/", $max_result);
        if ($rv == 1) {
            $this->set_error_message('between', $attribute, $v1, $v2);
            return false;
        }

        $min_result = bcsub($value, $v1);
        $rv         = preg_match("/^-.*$/", $min_result);
        if ($rv == 1) {
            $this->set_error_message('between', $attribute, $v1, $v2);
            return false;
        }
        return true;
    }

    protected function check_length_between($attribute, $value, $rule_data) {
        $div_arr = explode(',', $rule_data);
        if (count($div_arr) != 2) {
            throw new \Exception("Illegal PHPValidator expression: The 'length_between' rule's value must like ' min , max '");
        }
        $v1 = intval(trim($div_arr[0]));
        $v2 = intval(trim($div_arr[1]));
        if ($v2 < $v1) {
            $v  = $v1;
            $v1 = $v2;
            $v2 = $v;
        }

        $len = mb_strlen($value);
        if ($len < $v1 || $len > $v2) {
            $this->set_error_message('length_between', $attribute, $v1, $v2);
            return false;
        }
        return true;
    }

    protected function check_in($attribute, $value, $rule_data) {
        $div_arr = explode(',', $rule_data);
        if (!in_array($value, $div_arr)) {
            $this->set_error_message('in', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_not_in($attribute, $value, $rule_data) {
        $div_arr = explode(',', $rule_data);
        if (in_array($value, $div_arr)) {
            $this->set_error_message('not_in', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_filled($attribute, $value) {
        if (empty($value)) {
            $this->set_error_message('filled', $attribute);
            return false;
        }
        return true;
    }

    protected function check_distinct($attribute, $value) {
        if (!is_array($value)) {
            throw new \Exception("Illegal PHPValidator expression: Under the 'distinct' rule, the value must be a array");
        }
        if (count($value) != array_unique($value)) {
            $this->set_error_message('distinct', $attribute);
            return false;
        }
        return true;
    }

    protected function check_different($attribute, $rule_data) {
        $v1_exists = array_key_exists($rule_data, $this->data) ? $this->data[$rule_data] : null;
        $v2_exists = array_key_exists($attribute, $this->data) ? $this->data[$attribute] : null;
        if ($v1_exists === $v2_exists) {
            $this->set_error_message('different', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_same($attribute, $rule_data) {
        $v1_exists = array_key_exists($rule_data, $this->data) ? $this->data[$rule_data] : null;
        $v2_exists = array_key_exists($attribute, $this->data) ? $this->data[$attribute] : null;
        if ($v1_exists !== $v2_exists) {
            $this->set_error_message('same', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_required_with($attribute, $rule_data) {
        $div_arr      = explode(',', $rule_data);
        $we_need_you  = false;
        $who_not_null = "";
        foreach ($div_arr as $e_key) {
            if ($this->_check_not_null($e_key)) {
                $we_need_you  = true;
                $who_not_null = $e_key;
                break;
            }
        }

        if ($we_need_you && !$this->_check_not_null($attribute)) {
            $this->set_error_message('required_with', $attribute, $who_not_null);
            return false;
        }
        return true;
    }

    protected function check_required_with_all($attribute, $rule_data) {
        $div_arr     = explode(',', $rule_data);
        $we_need_you = true;
        foreach ($div_arr as $e_key) {
            if (!$this->_check_not_null($e_key)) {
                $we_need_you = false;
                break;
            }
        }
        if ($we_need_you && !$this->_check_not_null($attribute)) {
            $this->set_error_message('required_with_all', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_required_without($attribute, $rule_data) {
        $div_arr      = explode(',', $rule_data);
        $we_need_you  = false;
        $who_not_null = "";
        foreach ($div_arr as $e_key) {
            if (!$this->_check_not_null($e_key)) {
                $we_need_you  = true;
                $who_not_null = $e_key;
                break;
            }
        }
        if ($we_need_you && !$this->_check_not_null($attribute)) {
            $this->set_error_message('required_without', $attribute, $who_not_null);
            return false;
        }
        return true;
    }

    protected function check_required_without_all($attribute, $rule_data) {
        $div_arr     = explode(',', $rule_data);
        $we_need_you = true;
        foreach ($div_arr as $e_key) {
            if ($this->_check_not_null($e_key)) {
                $we_need_you = false;
                break;
            }
        }
        if ($we_need_you && !$this->_check_not_null($attribute)) {
            $this->set_error_message('required_without_all', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function add_alias($attribute, $rule_data) {
        $this->alias[$attribute] = $rule_data;
        return true;
    }

    protected function check_to_type($attribute, $rule_data) {
        if (!in_array($rule_data, $this->to_type_keys)) {
            $has_err = true;
            $div_arr = explode(':', $rule_data);
            if (count($div_arr) != 2) {
                goto goto_to_type_error;
            }
            $type       = $div_arr[0];
            $type_value = $div_arr[1];
            switch ($type) {
            case 'str_array':
                if (!in_array($type_value, $this->to_type_keys)) {
                    goto goto_to_type_error;
                }
                break;
            case 'scale':
                $rule_data = $type . ":" . intval($type_value);
                break;
            default:
                goto goto_to_type_error;
            }
            $has_err = false;
            goto_to_type_error:
            if ($has_err) {
                throw new \Exception("Illegal PHPValidator expression: The 'to_type' rule of parameter '{$attribute}' , '{$rule_data}' is not a valid type expression");
            }
            $this->to_type[$attribute] = $rule_data;
        }
        $this->to_type[$attribute] = $rule_data;
        return true;
    }

    protected function check_file_exists($attribute) {
        $file = $this->getFile($attribute);
        if ($file == []) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }
        $path = $file['tmp_name'];
        if (!file_exists($path)) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }
        return true;
    }

    protected function check_file_type_in($attribute, $rule_data) {
        $file = $this->getFile($attribute);
        if ($file == []) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }
        $type           = explode('/', $file['type'])[1];
        $type           = strtolower($type);
        $allow_type_arr = explode(',', $rule_data);
        $match          = false;
        foreach ($allow_type_arr as $allow_type) {
            if ($type == strtolower($allow_type)) {
                $match = true;
                break;
            }
        }
        if (!$match) {
            $this->set_error_message('file_type_in', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_file_max($attribute, $rule_data) {
        $file = $this->getFile($attribute);
        if ($file == []) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }
        $size = bcdiv($file['size'], 1048576, $this->scale);
        if ($size > floatval($rule_data)) {
            $this->set_error_message('file_max', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_file_min($attribute, $rule_data) {
        $file = $this->getFile($attribute);
        if ($file == []) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }
        $size = bcdiv($file['size'], 1048576, $this->scale);
        if ($size < floatval($rule_data)) {
            $this->set_error_message('file_min', $attribute, $rule_data);
            return false;
        }
        return true;
    }

    protected function check_file_size_between($attribute, $rule_data) {
        $file = $this->getFile($attribute);
        if ($file == []) {
            $this->set_error_message('file_exists', $attribute);
            return false;
        }

        $div_arr = explode(',', $rule_data);
        if (count($div_arr) != 2) {
            throw new \Exception("Illegal PHPValidator expression: The 'file_size_between' rule's value must like ' min , max '");
        }
        $v1 = floatval(trim($div_arr[0]));
        $v2 = floatval(trim($div_arr[1]));
        if ($v2 < $v1) {
            $v  = $v1;
            $v1 = $v2;
            $v2 = $v;
        }

        $size = bcdiv($file['size'], 1048576, $this->scale);
        if ($size < $v1 || $size > $v2) {
            $this->set_error_message('file_size_between', $attribute, $v1, $v2);
            return false;
        }
        return true;
    }

    protected function toType_str_array($attribute, $value, $type) {
        if (!isset($this->array_str[$attribute])) {
            $rule_result = $this->check_array_str($attribute, $value);
            if (!$rule_result) {
                return false;
            }
        }

        $this->data[$attribute] = explode(',', $this->array_str[$attribute]);
        foreach ($this->data[$attribute] as $key => $v) {
            switch ($type) {
            case 'string':$this->data[$attribute][$key] = strval($v);
                break;
            case 'boolean':$this->data[$attribute][$key] = boolval($v);
                break;
            case 'integer':$this->data[$attribute][$key] = intval($v);
                break;
            case 'float':$this->data[$attribute][$key] = floatval($v);
                break;
            case 'array':$this->data[$attribute][$key] = (array) $v;
                break;
            case 'object':$this->data[$attribute][$key] = (object) $v;
                break;
            default:
                break;
            }
        }
        return true;
    }

    protected function toType_scale($attribute, $value, $scale) {
        if (!isset($this->float[$attribute])) {
            if (!$this->check_float_str($attribute, $value)) {
                return false;
            }
        }

        $this->data[$attribute] = round($this->float[$attribute], intval($scale));
        return true;
    }
}
