<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 16:18
 */

use cdcchen\curl\HttpClient;
use cdcchen\curl\JsonFormatter;
use cdcchen\psr7\Request;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    public function testFormat()
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
        $formatter = new JsonFormatter();
        $request = new Request('post', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormat
     */
    public function testFormattedBody(Request $request)
    {
        $encoded = '{"user":{"name":"test-name","age":20},"content":"hi, test content.","like":["chen","dong"]}';
        $this->assertEquals($encoded, $request->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @depends testFormat
     */
    public function testFormattedContentTypeIs_application_json(Request $request)
    {
        $this->assertEquals('application/json; charset=utf-8', $request->getHeaderLine('content-type'));
    }

}