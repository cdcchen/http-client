<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/5/4
 * Time: 下午5:58
 */

namespace cdcchen\curl;


/**
 * Class Client
 * @package cdcchen\curl
 */

use cdcchen\psr7\HeaderCollection;
use cdcchen\psr7\Request;
use cdcchen\psr7\Uri;

/**
 * Trait Client
 * @package cdcchen\curl
 */
trait ClientTrait
{
    /**
     * Http GET method request shortcut
     *
     * @param string $url
     * @param null|array|string $queryParams
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function get(string $url, $queryParams = null, array $headers = [], array $options = []): HttpResponse
    {
        $uri = new Uri($url);

        if (is_array($queryParams)) {
            $queryString = http_build_query($queryParams);
        } elseif (is_string($queryParams)) {
            $queryString = $queryParams;
        } else {
            throw new \InvalidArgumentException('queryParams must be string or array.');
        }

        if ($queryString) {
            $uri->withQuery($queryString);
        }

        return static::sendRequest('GET', $uri, null, $headers, $options);
    }

    /**
     * Http POST method request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function post(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('POST', $url, $data, $headers, $options);
    }

    /**
     * Http PUT method request shortcut
     *
     * @param string $url
     * @param null|array $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function put(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('PUT', $url, $data, $headers, $options);
    }

    /**
     * Http GET method request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function head(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('HEAD', $url, $data, $headers, $options);
    }

    /**
     * Http PATCH method request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function patch(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('PATCH', $url, $data, $headers, $options);
    }

    /**
     * Http OPTIONS method request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function options(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('OPTIONS', $url, $data, $headers, $options);
    }

    /**
     * Http DELETE method request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function delete(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('DELETE', $url, $data, $headers, $options);
    }

    /**
     * Http upload request shortcut
     *
     * @param string $url
     * @param null|array|string $data
     * @param array $files [inputName => file] or [inputName => [file1, file2]]
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     */
    public static function upload(
        string $url,
        array $files = [],
        $data = null,
        array $headers = [],
        array $options = []
    ): HttpResponse {
        return static::sendRequest('POST', $url, $data, $headers, $options, $files);
    }

    /**
     * @param string $method
     * @param string $url
     * @param null|string $body
     * @param array $headers
     * @param array $options
     * @param \CURLFile[] $files [inputName => file] or [inputName => [file1, file2]]
     * @return HttpResponse
     */
    private static function sendRequest(
        string $method,
        string $url,
        $body = null,
        array $headers,
        array $options,
        array $files = []
    ): HttpResponse {
        $request = new Request($method, $url, new HeaderCollection($headers));
        $client = (new HttpClient())->addOptions($options);
        if ($body) {
            $client->setData($body);
        }

        foreach ($files as $inputName => $file) {
            if (is_array($file)) {
                $client->addFiles($inputName, $file);
            } else {
                $client->addFile($inputName, $file);
            }
        }

        return $client->request($request);
    }
}