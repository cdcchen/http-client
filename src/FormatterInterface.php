<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/11
 * Time: 15:43
 */

namespace cdcchen\curl;

use Psr\Http\Message\RequestInterface;


/**
 * Interface FormatterInterface
 * @package cdcchen\curl
 */
interface FormatterInterface
{
    /**
     * Formats given HTTP request message.
     * @param HttpClient $client
     * @param RequestInterface $request HTTP request instance
     * @return RequestInterface
     */
    public function format(HttpClient $client, RequestInterface $request): RequestInterface;
}