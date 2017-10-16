<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 17:58
 */

namespace cdcchen\curl;

use cdcchen\psr7\Response;


/**
 * Interface ParserInterface
 * @package cdcchen\curl
 */
interface ParserInterface
{
    /**
     * Parses given HTTP response instance.
     * @param Response $response HTTP response instance.
     * @return array parsed content data.
     */
    public function parse(Response $response): array;
}