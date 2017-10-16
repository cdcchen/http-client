<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 17:59
 */

namespace cdcchen\curl;

use cdcchen\psr7\Response;


/**
 * Class XmlParser
 * @package cdcchen\curl
 */
class XmlParser implements ParserInterface
{
    /**
     * @param Response $response
     * @return array
     */
    public function parse(Response $response): array
    {
        return $this->convertXmlToArray($response->getBody()->getContents());
    }

    /**
     * Converts XML document to array.
     * @param string|\SimpleXMLElement $xml xml to process.
     * @return array XML array representation.
     */
    protected function convertXmlToArray($xml)
    {
        if (!is_object($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $result = (array)$xml;
        foreach ($result as $key => $value) {
            if (is_object($value)) {
                $result[$key] = $this->convertXmlToArray($value);
            }
        }
        return $result;
    }
}