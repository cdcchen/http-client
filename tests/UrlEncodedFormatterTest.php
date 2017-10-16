<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 16:18
 */

use cdcchen\curl\HttpClient;
use cdcchen\curl\UrlEncodedFormatter;
use cdcchen\psr7\Request;
use PHPUnit\Framework\TestCase;

class UrlEncodedFormatterTest extends TestCase
{
    const STR_RFC1738 = 'user%5Bname%5D=test-name&user%5Bage%5D=20&content=hi%2C+test+content.&like%5B0%5D=chen&like%5B1%5D=dong';
    const STR_RFC3986 = 'user%5Bname%5D=test-name&user%5Bage%5D=20&content=hi%2C%20test%20content.&like%5B0%5D=chen&like%5B1%5D=dong';

    public function testFormatRFC1738MethodIsGet()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
            'like' => ['chen', 'dong'],
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new UrlEncodedFormatter();
        $request = new Request('get', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatRFC1738MethodIsGet
     */
    public function testFormattedUriQueryRFC1738(Request $request)
    {
        $this->assertContains(self::STR_RFC1738, $request->getUri()->getQuery());
    }

    public function testFormatRFC1738MethodIsPost()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
            'like' => ['chen', 'dong'],
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new UrlEncodedFormatter();
        $request = new Request('post', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatRFC1738MethodIsPost
     */
    public function testFormattedBodyRFC1738(Request $request)
    {
        $this->assertEquals(self::STR_RFC1738, $request->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @depends testFormatRFC1738MethodIsPost
     */
    public function testFormattedContentTypeIs_application_x_www_form_urlencoded(Request $request)
    {
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaderLine('content-type'));
    }

    ############################### PHP_QUERY_RFC3986 #################################

    public function testFormatRFC3986MethodIsGet()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
            'like' => ['chen', 'dong'],
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new UrlEncodedFormatter();
        $formatter->encodingType = PHP_QUERY_RFC3986;
        $request = new Request('get', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatRFC3986MethodIsGet
     */
    public function testFormattedUriQueryRFC3986(Request $request)
    {
        $this->assertContains(self::STR_RFC3986, $request->getUri()->getQuery());
    }

    public function testFormatRFC3986MethodIsPost()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
            'like' => ['chen', 'dong'],
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new UrlEncodedFormatter();
        $formatter->encodingType = PHP_QUERY_RFC3986;
        $request = new Request('post', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatRFC3986MethodIsPost
     */
    public function testFormattedBodyRFC3986(Request $request)
    {
        $this->assertEquals(self::STR_RFC3986, $request->getBody()->getContents());
    }

}