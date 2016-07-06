<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 25.11.2015
 * Time: 22:11
 */

namespace engine\components;

class Json {
    /**
     * Encodes the given value into a JSON string.
     * @param mixed $value the data to be encoded.
     * @param integer $options the encoding options. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>. Default is `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     * @return string the encoding result.
     */
    public static function encode($value, $options = 320)
    {
        return json_encode($value, $options);
    }

    /**
     * Encodes the given value into a JSON string HTML-escaping entities so it is safe to be embedded in HTML code.
     *
     * @param mixed $value the data to be encoded
     * @return string the encoding result
     */
    public static function htmlEncode($value)
    {
        return static::encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
    }

    /**
     * Decodes the given JSON string into a PHP data structure.
     * @param string $json the JSON string to be decoded
     * @param boolean $asArray whether to return objects in terms of associative arrays.
     * @return mixed the PHP data
     */
    public static function decode($json, $asArray = true)
    {
        return json_decode((string) $json, $asArray);
    }

}