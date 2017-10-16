<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 18:44
 */

namespace cdcchen\curl;

use cdcchen\psr7\Response;


/**
 * Class UrlEncodedParser
 * @package cdcchen\curl
 */
class UrlEncodedParser implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(Response $response): array
    {
        $data = [];
        parse_str($response->getBody()->getContents(), $data);
        return $data;
    }
}