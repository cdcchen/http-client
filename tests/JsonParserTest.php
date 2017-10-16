<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 15:53
 */

use PHPUnit\Framework\TestCase;
use cdcchen\curl\JsonParser;
use cdcchen\psr7\Response;

class JsonParserTest extends TestCase
{
    public function testParse()
    {
        $jsonEncoded = '{"data":{"user":{"name":"test-name", "age":20},"content":"hi, test comment."}}';
        $parser = new JsonParser();
        $response = new Response(Response::STATUS_OK, null, $jsonEncoded);
        $data = $parser->parse($response);

        $this->assertTrue(is_array($data));
        return $data;
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
    public function testParsedDataHasKey_User_Name(array $data)
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