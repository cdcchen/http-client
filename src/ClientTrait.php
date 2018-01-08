<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/5/4
 * Time: 下午5:58
 */

namespace cdcchen\http;


/**
 * Class Client
 * @package cdcchen\http
 */

use cdcchen\psr7\HeaderCollection;
use cdcchen\psr7\Request;
use cdcchen\psr7\StreamHelper;
use cdcchen\psr7\Uri;
use Psr\Http\Message\StreamInterface;

/**
 * Trait Client
 * @package cdcchen\http
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
     * @throws RequestException
     */
    public static function get(string $url, $queryParams = null, array $headers = [], array $options = []): HttpResponse
    {
        $uri = new Uri($url);

        if (is_array($queryParams)) {
            $queryParams = http_build_query($queryParams);
        }
        if ($queryParams && !is_string($queryParams)) {
            throw new \InvalidArgumentException('queryParams must be string or array.');
        }

        if ($queryParams) {
            $uri->withQuery($queryParams);
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
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
     * @throws RequestException
     */
    public static function delete(string $url, $data = null, array $headers = [], array $options = []): HttpResponse
    {
        return static::sendRequest('DELETE', $url, $data, $headers, $options);
    }

    /**
     * Http upload request shortcut
     *
     * @param string $url
     * @param array $files [inputName => file] or [inputName => [file1, file2]]
     * @param null|array|string $data
     * @param array $headers
     * @param array $options
     * @return HttpResponse
     * @throws RequestException
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
     * @throws RequestException
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

        if ($body !== null) {
            if (is_string($body)) {
                $body = StreamHelper::createStream($body);
            }
            if ($body instanceof StreamInterface) {
                $request = $request->withBody($body);
            } else {
                $client->setData($body);
            }
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