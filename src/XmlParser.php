<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 17:59
 */

namespace cdcchen\http;

use cdcchen\psr7\Response;


/**
 * Class XmlParser
 * @package cdcchen\http
 */
class XmlParser implements ParserInterface
{
    /**
     * @param Response $response
     * @return array
     */
    public function parse(Response $response): array
    {
        return static::xmlToArray((string)$response->getBody());
    }

    /**
     * Converts XML document to array.
     * @param string|\SimpleXMLElement $xml xml to process.
     * @return array XML array representation.
     */
    public static function xmlToArray($xml)
    {
        if (!is_object($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $result = (array)$xml;
        foreach ($result as $key => $value) {
            if (is_object($value)) {
                $result[$key] = static::xmlToArray($value);
            }
        }
        return $result;
    }
}