<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/8/20
 * Time: 10:57
 */

namespace cdcchen\curl;


use InvalidArgumentException;

class Formatter
{
    /**
     * JSON format
     */
    const FORMAT_JSON = 'json';
    /**
     * urlencoded by RFC1738 query string, like name1=value1&name2=value2
     * @see http://php.net/manual/en/function.urlencode.php
     */
    const FORMAT_URLENCODED = 'urlencoded';
    /**
     * urlencoded by PHP_QUERY_RFC3986 query string, like name1=value1&name2=value2
     * @see http://php.net/manual/en/function.rawurlencode.php
     */
    const FORMAT_RAW_URLENCODED = 'raw-urlencoded';
    /**
     * XML format
     */
    const FORMAT_XML = 'xml';

    /**
     * @param string $format
     * @return bool
     */
    public static function isValidFormat(string $format): bool
    {
        return in_array($format, [
            self::FORMAT_JSON,
            self::FORMAT_URLENCODED,
            self::FORMAT_RAW_URLENCODED,
            self::FORMAT_XML
        ]);
    }

    /**
     * Returns HTTP message formatter instance for the specified format.
     * @return FormatterInterface formatter instance.
     * @throws InvalidArgumentException on invalid format name.
     */
    public static function getFormatter(string $format)
    {
        static $defaultFormatters = [
            Formatter::FORMAT_JSON => 'cdcchen\curl\JsonFormatter',
            Formatter::FORMAT_URLENCODED => [
                'class' => 'cdcchen\curl\UrlEncodedFormatter',
                'encodingType' => PHP_QUERY_RFC1738
            ],
            Formatter::FORMAT_RAW_URLENCODED => [
                'class' => 'cdcchen\curl\UrlEncodedFormatter',
                'encodingType' => PHP_QUERY_RFC3986
            ],
            Formatter::FORMAT_XML => 'cdcchen\curl\XmlFormatter',
        ];

        if (!isset($defaultFormatters[$format])) {
            throw new InvalidArgumentException("Unrecognized format '{$format}'");
        }

        $formatter = $defaultFormatters[$format];
        if (!is_object($formatter)) {
            if (is_array($formatter) && isset($formatter['class'])) {
                $className = $formatter['class'];
                $encodingType = $formatter['encodingType'];
                $formatter = new $className;
                $formatter->encodingType = $encodingType;
            } else {
                $formatter = new $formatter();
            }
        }

        return $formatter;
    }

}