<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 15:32
 */

use PHPUnit\Framework\TestCase;
use cdcchen\curl\XmlParser;
use cdcchen\psr7\Response;

class XmlParserTest extends TestCase
{
    public function testParse()
    {
        $xml = '<data><user><name>test-name</name><age>20</age></user><content>hi, test comment.</content></data>';
        $parser = new XmlParser();
        $response = new Response(Response::STATUS_OK, null, $xml);
        $data = $parser->parse($response);

        $this->assertTrue(is_array($data));
        return $data;
    }

    /**
     * @param array $data
     * @depends testParse
     */
    public function testParsedDataHasKeyUser(array $data)
    {
        $this->assertArrayHasKey('user', $data);
    }

    /**
     * @param array $data
     * @depends testParse
     */
    public function testParsedDataHasKeyContent(array $data)
    {
        $this->assertArrayHasKey('content', $data);
    }

    /**
     * @param array $data
     * @depends testParse
     */
    public function testParsedDataHasKey(array $data)
    {
        $this->assertArrayHasKey('name', $data['user']);
    }

    /**
     * @param array $data
     * @depends testParse
     */
    public function testParsedDataUserNameIsTestName(array $data)
    {
        $this->assertEquals('test-name', $data['user']['name']);
    }
}