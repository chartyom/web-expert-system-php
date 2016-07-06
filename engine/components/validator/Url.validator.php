<?php
namespace engine\components\validator;

class UrlValidator{
    /**
     * @var string the regular expression used to validate the attribute value.
     * The pattern may contain a `{schemes}` token that will be replaced
     * by a regular expression which represents the [[validSchemes]].
     */
    private static $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
    /**
     * @var array list of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     */
    private static $validSchemes = ['http', 'https'];
    /**
     * @var string the default URI scheme. If the input doesn't contain the scheme part, the default
     * scheme will be prepended to it (thus changing the input). Defaults to null, meaning a URL must
     * contain the scheme part.
     */
    private static $defaultScheme;

    public static function url($url){
        // make sure the length is limited to avoid DOS attacks
        if (is_string($url) && strlen($url) < 2000) {
            if (strpos(self::$pattern, '{schemes}') !== false) {
                $pattern = str_replace('{schemes}', '(' . implode('|', self::$validSchemes) . ')', self::$pattern);
            } else {
                $pattern = self::$pattern;
            }

            if (preg_match($pattern, $url)) {
                return true;
            }

            return false;

        }
        return false;
    }
}