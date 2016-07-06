<?php
namespace engine\components;


class Html{
    /**
     * @var array list of void elements (element name => 1)
     * @see http://www.w3.org/TR/html-markup/syntax.html#void-element
     */
    private static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];

    /**
     * @var array the preferred order of attributes in a tag. This mainly affects the order of the attributes
     * that are rendered by [[renderTagAttributes()]].
     */
    private static $attributeOrder = [
        'type',
        'id',
        'class',
        'name',
        'value',

        'href',
        'src',
        'action',
        'method',

        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',

        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',

        'alt',
        'title',
        'rel',
        'media',
    ];

    /**
     * @var array list of tag attributes that should be specially handled when their values are of array type.
     * In particular, if the value of the `data` attribute is `['name' => 'xyz', 'age' => 13]`, two attributes
     * will be generated instead of one: `data-name="xyz" data-age="13"`.
     */
    private static $dataAttributes = ['data', 'data-ng', 'ng'];

    /**
     * Encodes special characters into HTML entities.
     * @param string $content the content to be encoded
     * @param boolean $doubleEncode whether to encode HTML entities in `$content`. If false,
     * HTML entities in `$content` will not be further encoded.
     * @return string the encoded content
     * @see decode()
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * Decodes special HTML entities back to the corresponding characters.
     * This is the opposite of [[encode()]].
     * @param string $content the content to be decoded
     * @return string the decoded content
     * @see encode()
     * @see http://www.php.net/manual/en/function.htmlspecialchars-decode.php
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * Generates a complete HTML tag.
     * @param string $name the tag name
     * @param string $content the content to be enclosed between the start and end tags. It will not be HTML-encoded.
     * If this is coming from end users, you should consider [[encode()]] it to prevent XSS attacks.
     * @param array $options the HTML tag attributes (HTML options) in terms of name-value pairs.
     * These will be rendered as the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     * If a value is null, the corresponding attribute will not be rendered.
     *
     * For example when using `['class' => 'my-class', 'target' => '_blank', 'value' => null]` it will result in the
     * html attributes rendered like this: `class="my-class" target="_blank"`.
     *
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated HTML tag
     * @see beginTag()
     * @see endTag()
     */
    private static function tag($name, $content = '', $options = [])
    {
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
    }

    /**
     * Generates a start tag.
     * @param string $name the tag name
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     * If a value is null, the corresponding attribute will not be rendered.
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     * @return string the generated start tag
     * @see endTag()
     * @see tag()
     */
    private static function beginTag($name, $options = [])
    {
        return "<$name" . static::renderTagAttributes($options) . '>';
    }

    /**
     * Generates an end tag.
     * @param string $name the tag name
     * @return string the generated end tag
     * @see beginTag()
     * @see tag()
     */
    private static function endTag($name)
    {
        return "</$name>";
    }

    /**
     * Generates a style tag.
     * @param string $content the style content
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     * If a value is null, the corresponding attribute will not be rendered.
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     * @return string the generated style tag
     */
    public static function style($content, $options = [])
    {
        return static::tag('style', $content, $options);
    }

    /**
     * Generates a script tag.
     * @param string $content the script content
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     * If a value is null, the corresponding attribute will not be rendered.
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     * @return string the generated script tag
     */
    public static function script($content, $options = [])
    {
        return static::tag('script', $content, $options);
    }

    /**
     * Generates a link tag that refers to an external CSS file.
     * @param array|string $url the URL of the external CSS file. This parameter will be processed by [[Url::to()]].
     * @param array $options the tag options in terms of name-value pairs. The following option is specially handled:
     *
     * - condition: specifies the conditional comments for IE, e.g., `lt IE 9`. When this is specified,
     *   the generated `link` tag will be enclosed within the conditional comments. This is mainly useful
     *   for supporting old versions of IE browsers.
     * - noscript: if set to true, `link` tag will be wrapped into `<noscript>` tags.
     *
     * The rest of the options will be rendered as the attributes of the resulting link tag. The values will
     * be HTML-encoded using [[encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     * @return string the generated link tag
     * @see Url::to()
     */
    public static function cssFile($url, $options = [])
    {
        $url = '/css/'.$url;

        if (!isset($options['rel'])) {
            $options['rel'] = 'stylesheet';
        }
        $options['href'] = Url::to($url);

        if (isset($options['condition'])) {
            $condition = $options['condition'];
            unset($options['condition']);
            return self::wrapIntoCondition(static::tag('link', '', $options), $condition);
        } elseif (isset($options['noscript']) && $options['noscript'] === true) {
            unset($options['noscript']);
            return "<noscript>" . static::tag('link', '', $options) . "</noscript>";
        } else {
            return static::tag('link', '', $options);
        }
    }

    /**
     * Generates a script tag that refers to an external JavaScript file.
     * @param string $url the URL of the external JavaScript file. This parameter will be processed by [[Url::to()]].
     * @param array $options the tag options in terms of name-value pairs. The following option is specially handled:
     *
     * - condition: specifies the conditional comments for IE, e.g., `lt IE 9`. When this is specified,
     *   the generated `script` tag will be enclosed within the conditional comments. This is mainly useful
     *   for supporting old versions of IE browsers.
     *
     * The rest of the options will be rendered as the attributes of the resulting script tag. The values will
     * be HTML-encoded using [[encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     * @return string the generated script tag
     * @see Url::to()
     */
    public static function jsFile($url, $options = [])
    {
        $url = '/js/'.$url;

        $options['src'] = Url::to($url);
        if (isset($options['condition'])) {
            $condition = $options['condition'];
            unset($options['condition']);
            return self::wrapIntoCondition(static::tag('script', '', $options), $condition);
        } else {
            return static::tag('script', '', $options);
        }
    }


    /**
     * @return string
     * @author Artem
     */
    public static function metaViewport()
    {
        return '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
	    /*only javascript app*/
	    /*<meta name="apple-mobile-web-app-capable" content="yes" />*/
    }

    /**
     * @return string
     * @author Artem
     */
    public static function metaIEEdge()
    {
        return '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    }

    /**
     * Wraps given content into conditional comments for IE, e.g., `lt IE 9`.
     * @param string $content raw HTML content.
     * @param string $condition condition string.
     * @return string generated HTML.
     */
    private static function wrapIntoCondition($content, $condition)
    {
        if (strpos($condition, '!IE') !== false) {
            return "<!--[if $condition]><!-->\n" . $content . "\n<!--<![endif]-->";
        }
        return "<!--[if $condition]>\n" . $content . "\n<![endif]-->";
    }


    /**
     * Проверяет ссылку на соответствие заданному массиву.
     * В конечном результате выводит заданный параметр $return при условии,
     * что соответствуют все элементы главного массива и частично соответствуют элементы вложенного массива всем элементам GET запроса.
     *
     * Можно использовать вложенные массивы второго порядка.
     * Например: ['base'=>'user',['id'=>1,'url'=>'UserNamePage']]
     * Это используется в случае, когда одна страница может иметь несколько значений соответствия
     *
     * Например: ['base'=>'user','tab'=>['','about']];
     * Используется, когда страница может принимать несколько значений
     *
     * @author Черняев Артём
     *
     * @param array  $array
     * @param string $return
     *
     * @return string
     */
    public static function getActiveLink($array = ['base'=>'user','act'=>'',['id'=>1,'url'=>'UserNamePage']],$return = 'active'){

        $result= '';
        /*Первый массив отвечает за точные значения*/
        foreach($array as $key=>$value){
            if(is_array($value)){
                /*
                 * Если идентификатор является текстовым, то
                 * страница может принимать несколько значений
                 * ['base'=>'user','tab'=>['','about']]
                 */
                if(is_string($key)){
                    foreach($value as $keyChild=>$valueChild){
                        if(is_array($valueChild)){
                            $result = '';
                            break;
                        }else {
                            /*Если элемент найдет соответствий с главным идентификатором $key,
                             то выйдет из цикла
                            */
                            if(Request::get($key)==$valueChild){
                                $result = $return;
                                break;
                            } else {
                                $result = '';
                            }
                        }
                    }
                } else {
                    /*Второй массив отвечает за возможные значения*/
                    foreach($value as $keyChild=>$valueChild){
                        if(is_array($valueChild)){
                            $result = '';
                            break;
                        }else {
                            /*Выход из цикла после нахождения любого из указанных элементов*/
                            if(Request::get($keyChild)==$valueChild){
                                $result = $return;
                                break;
                            } else {
                                $result = '';
                            }
                        }
                    }
                }

            }else {
                /*Если элемент не найден, тогда выход из цикла*/
                if(Request::get($key)==$value){
                    $result = $return;
                } else {
                    $result = '';
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public static function beginHtml()
    {
        return '<!DOCTYPE html><html lang="en">';
    }

    /**
     * @return string
     */
    public static function endHtml()
    {
        return self::endTag("html");
    }


    /**
     *  <title>$title</title>
     *
     * @param string $title
     *
     * @return string
     * @author Artem
     */
    public static function title($title = 'default')
    {
        return self::tag("title",$title);
    }


    /**
     * <meta charset='$format'>
     *
     * @param string $format
     *
     * @return string
     */
    public static function charset($format = ENGINE_FORMAT)
    {
        return "<meta charset='$format'>";
    }

    /**
     * @param string $description
     *
     * @return string
     */
    public static function description($description = 'default')
    {
        return "<meta name='description' Content='$description'>";
    }

    /**
     * @param string $folder
     *
     * @return string
     * @author Artem
     */
    public static function favicon($folder = "default")
    {
        $tags = null;
        $files = [
            'favicon-196x196.png'=>'<link rel="icon" type="image/png" href="'.Url::home().'/favicon/'.$folder.'/favicon-196x196.png" sizes="196x196">',
            'favicon-160x160.png'=>'<link rel="icon" type="image/png" href="'.Url::home().'/favicon/'.$folder.'/favicon-160x160.png" sizes="160x160">',
            'favicon-96x96.png'=>'<link rel="icon" type="image/png" href="'.Url::home().'/favicon/'.$folder.'/favicon-96x96.png" sizes="96x96">',
            'favicon-32x32.png'=>'<link rel="icon" type="image/png" href="'.Url::home().'/favicon/'.$folder.'/favicon-32x32.png" sizes="32x32">',
            'favicon-16x16.png'=>'<link rel="icon" type="image/png" href="'.Url::home().'/favicon/'.$folder.'/favicon-16x16.png" sizes="16x16">',
            'apple-touch-icon-180x180.png'=>'<link rel="apple-touch-icon" sizes="180x180" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-180x180.png">',
            'apple-touch-icon-152x152.png'=>'<link rel="apple-touch-icon" sizes="152x152" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-152x152.png">',
            'apple-touch-icon-144x144.png'=>'<link rel="apple-touch-icon" sizes="144x144" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-144x144.png">',
            'apple-touch-icon-120x120.png'=>'<link rel="apple-touch-icon" sizes="120x120" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-120x120.png">',
            'apple-touch-icon-114x114.png'=>'<link rel="apple-touch-icon" sizes="114x114" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-114x114.png">',
            'apple-touch-icon-76x76.png'=>'<link rel="apple-touch-icon" sizes="76x76" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-76x76.png">',
            'apple-touch-icon-72x72.png'=>'<link rel="apple-touch-icon" sizes="72x72" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-72x72.png">',
            'apple-touch-icon-60x60.png'=>'<link rel="apple-touch-icon" sizes="60x60" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-60x60.png">',
            'apple-touch-icon-57x57.png'=>'<link rel="apple-touch-icon" sizes="57x57" href="'.Url::home().'/favicon/'.$folder.'/apple-touch-icon-57x57.png">',
            'mstile-144x144.png'=>'<meta name="msapplication-TileColor" content="#2b5797"><meta name="msapplication-TileImage" content="'.Url::home().'/favicon/'.$folder.'/mstile-144x144.png">',
            'favicon.ico'=>'<!--[if IE]><link rel="shortcut icon" href="'.Url::home().'/favicon/'.$folder.'/favicon.ico"/><![endif]-->'
        ];

        foreach ($files AS $key=>$value){
            if ( file_exists (MAIN_PATH.'/favicon/'.$folder.'/'.$key)) {
                $tags = $tags.$value ;
            }
        }

        return $tags;

    }



    /**
     * Renders the HTML tag attributes.
     *
     * Attributes whose values are of boolean type will be treated as
     * [boolean attributes](http://www.w3.org/TR/html5/infrastructure.html#boolean-attributes).
     *
     * Attributes whose values are null will not be rendered.
     *
     * The values of attributes will be HTML-encoded using [[encode()]].
     *
     * The "data" attribute is specially handled when it is receiving an array value. In this case,
     * the array will be "expanded" and a list data attributes will be rendered. For example,
     * if `'data' => ['id' => 1, 'name' => 'jane']`, then this will be rendered:
     * `data-id="1" data-name="jane"`.
     * Additionally `'data' => ['params' => ['id' => 1, 'name' => 'jane'], 'status' => 'ok']` will be rendered as:
     * `data-params='{"id":1,"name":"jane"}' data-status="ok"`.
     *
     * @param array $attributes attributes to be rendered. The attribute values will be HTML-encoded using [[encode()]].
     * @return string the rendering result. If the attributes are not empty, they will be rendered
     * into a string with a leading white space (so that it can be directly appended to the tag name
     * in a tag. If there is no attribute, an empty string will be returned.
     */
    private static function renderTagAttributes($attributes)
    {
        if (count($attributes) > 1) {
            $sorted = [];
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " $name-$n='" . Json::htmlEncode($v) . "'";
                        } else {
                            $html .= " $name-$n=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " $name='" . Json::htmlEncode($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }



    /**
     * Converts a CSS style array into a string representation.
     *
     * For example,
     *
     * ```php
     * print_r(Html::cssStyleFromArray(['width' => '100px', 'height' => '200px']));
     * // will display: 'width: 100px; height: 200px;'
     * ```
     *
     * @param array $style the CSS style array. The array keys are the CSS property names,
     * and the array values are the corresponding CSS property values.
     * @return string the CSS style string. If the CSS style is empty, a null will be returned.
     */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        // return null if empty to avoid rendering the "style" attribute
        return $result === '' ? null : rtrim($result);
    }

    /**
     * Converts a CSS style string into an array representation.
     *
     * The array keys are the CSS property names, and the array values
     * are the corresponding CSS property values.
     *
     * For example,
     *
     * ```php
     * print_r(Html::cssStyleToArray('width: 100px; height: 200px;'));
     * // will display: ['width' => '100px', 'height' => '200px']
     * ```
     *
     * @param string $style the CSS style string
     * @return array the array representation of the CSS style
     */
    public static function cssStyleToArray($style)
    {
        $result = [];
        foreach (explode(';', $style) as $property) {
            $property = explode(':', $property);
            if (count($property) > 1) {
                $result[trim($property[0])] = trim($property[1]);
            }
        }
        return $result;
    }
}