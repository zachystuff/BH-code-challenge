<?php

namespace AS\Utilities;

class Utils {
     /**
     * @param array<string, array<'ARRAY'|'POST'|'GET'|'FILES'>> $params
     *
     * @return array<string, array|string|null>
     */
    public static function read_params(array $params) {
        $form = [];
        foreach ($params as $name => $methods_allowed) {
            $form[$name] = self::read_param($name, $methods_allowed);
        }
        return $form;
    }

    /**
     * Note: there is a logic issue in this method where $found is calculated
     * but never used; if you have arrived here due to buggy behavior that is
     * a good place to start troubleshooting.
     *
     * @param string $name
     * @param array<'ARRAY'|'POST'|'GET'|'FILES'> $methods_allowed
     *
     * @return array|string|null
     */
    public static function read_param($name, array $methods_allowed = array()) {
        if (empty($methods_allowed)) {
            $methods_allowed[] = 'POST';
        }
        $exists = false;
        $param = false;

        foreach ($methods_allowed as $method) {
            switch ($method) {
                case 'ARRAY':
                    if (strpos($name, '[')) {
                        $arr = explode('[', str_replace(']', '', $name));
                        $param = (isset($_POST[$arr[0]][$arr[1]]) ? $_POST[$arr[0]][$arr[1]] : false);
                        $exists = true;
                    }
                    break;
                case 'POST':
                    if (isset($_POST[$name])) {
                        $param = $_POST[$name];
                        $exists = true;
                    }
                    break;
                case 'GET':
                    if (isset($_GET[$name])) {
                        $param = $_GET[$name];
                        $exists = true;
                    }
                    break;
                case 'FILES':
                    if (isset($_FILES[$name])) {
                        $param = $_FILES[$name];
                        $exists = true;
                    }
                    break;
            }
            if ($exists) {
                break;
            }
        }
        if (!$exists) {
            foreach (array('ARRAY', 'POST', 'GET', 'FILES') as $method) {
                $found = false;
                // If found here, it came in the wrong method and will be ignored.
                switch ($method) {
                    case 'ARRAY':
                        if (strpos($name, '[')) {
                            $found = true;
                        }
                        break;
                    case 'POST':
                        if (isset($_POST[$name])) {
                            $found = true;
                        }
                        break;
                    case 'GET':
                        if (isset($_GET[$name])) {
                            $found = true;
                        }
                        break;
                    case 'FILES':
                        if (isset($_FILES[$name])) {
                            $found = true;
                        }
                        break;
                }
                if ($found) {
                    break;
                }
            }
        }

        if ($param === false && !$exists) {
            return null;
        } else {
            return $param;
        }
    }

        /**
     * @param array<string, array{type: 'array'|'bool'|'float'|'int'|'string', methods_allowed?: string[], default?: array|scalar|null}> $params
     *
     * @return array<string, array|scalar|null>
     */
    public static function read_typed_params(array $params, bool $typed_params_warning = false) {
        $form = [];
        foreach ($params as $name => $param_data) {
            if (isset($param_data['methods_allowed'])) {
                $methods_allowed = $param_data['methods_allowed'];
            } else {
                $methods_allowed = [];
            }
            $type = $param_data['type'];
            $value = self::read_param($name, $methods_allowed);
            if ((is_null($value)) && isset($param_data['default'])) {
                $value = $param_data['default'];
            }
            if ($type == 'array') { // sometimes this ends up passing through non-array values
                $form[$name] = $value;
            } elseif (!is_string($value)) {
                // if $value isn't a string, cast_form_param is gonna fail, return null or the default value
                $form[$name] = null;
                if (isset($param_data['default'])) {
                    $form[$name] = $param_data['default'];
                }
            } else {
                $form[$name] = self::cast_form_param($value, $type, $typed_params_warning);
            }
        }

        return $form;
    }

    public static function cast_form_param(?string $value, array|bool|float|int|string $type, bool $typed_params_warning = false) {
        if (!is_null($value)) {
            switch ($type) {
                case 'int':
                    $value = (int)filter_var($value, FILTER_VALIDATE_INT);
                    break;
                case 'float':
                    $value = (float)filter_var($value, FILTER_VALIDATE_FLOAT);
                    break;
                case 'bool':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
            }
        }
        return $value;
    }
}