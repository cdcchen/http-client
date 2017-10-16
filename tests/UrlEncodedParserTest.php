<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 16:10
 */

use PHPUnit\Framework\TestCase;
use cdcchen\curl\UrlEncodedParser;
use cdcchen\psr7\Response;

class UrlEncodedParserTest extends TestCase
{
    public function testParse()
    {
        $text = 'type=userinfo&data[user][name]=test-name&data[user][age]=20&data[content]=hi, test comment.';
        $parser = new UrlEncodedParser();
        $response = new Response(Response::STATUS_OK, null, $text);
        $data = $parser->parse($response);

        $this->assertTrue(is_array($data));
        return $data;
    }

    /**
     * @param array $data
     * @depends testParse
     * @return mixed
     */
    public function testParsedDataHasKeyType(array $data)
    {
        $this->assertArrayHasKey('type', $data);
    }

    /**
     * @param array $data
     * @depends testParse
     * @return mixed
     */
    public function testParsedDataHasKeyData(array $data)
    {
        $this->assertArrayHasKey('data', $data);
        return $data['data'];
    }

    /**
     * @param array $data
     * @depends testParsedDataHasKeyData
     */
    public function testParsedDataHasKeyUser(array $data)
    {
        $this->assertArrayHasKey('user', $data);
    }

    /**
     * @param array $data
     * @depends testParsedDataHasKeyData
     */
    public function testParsedDataHasKeyContent(array $data)
    {
        $this->assertArrayHasKey('content', $data);
    }

    /**
     * @param array $data
     * @depends testParsedDataHasKeyData
     */
    public function testParsedDataHasKey(array $data)
    {
        $this->assertArrayHasKey('name', $data['user']);
    }

    /**
     * @param array $data
     * @depends testParsedDataHasKeyData
     */
    public function testParsedDataUserNameIsTestName(array $data)
    {
        $this->assertEquals('test-name', $data['user']['name']);
    }
}