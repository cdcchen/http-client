<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/28
 * Time: 16:18
 */

use cdcchen\curl\HttpClient;
use cdcchen\curl\XmlFormatter;
use cdcchen\psr7\Request;
use PHPUnit\Framework\TestCase;

class XmlFormatterTest extends TestCase
{
    public function testFormatEncodingIsUtf8()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new XmlFormatter();
        $formatter->encoding = 'utf-8';
        $request = new Request('post', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatEncodingIsUtf8
     */
    public function testFormattedBodyEncodingIsUtf8(Request $request)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<request><user><name>test-name</name><age>20</age></user><content>hi, test content.</content></request>' . "\n";
        $this->assertEquals($xml, $request->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @depends testFormatEncodingIsUtf8
     */
    public function testFormattedEncodingIsUtf8ContentTypeIs_application_json(Request $request)
    {
        $this->assertEquals('application/xml; charset=utf-8', $request->getHeaderLine('content-type'));
    }


    ############################### encoding gbk ####################################

    public function testFormatEncodingIsGBK()
    {
        $data = [
            'user' => [
                'name' => 'test-name',
                'age' => 20,
            ],
            'content' => 'hi, test content.',
        ];
        $client = (new HttpClient())->setData($data);
        $formatter = new XmlFormatter();
        $formatter->encoding = 'gbk';
        $request = new Request('post', 'http://127.0.0.1/?format=json');
        $request = $formatter->format($client, $request);

        $this->assertInstanceOf(Request::class, $request);

        return $request;
    }

    /**
     * @param Request $request
     * @depends testFormatEncodingIsGBK
     */
    public function testFormattedBodyEncodingIsGBK(Request $request)
    {
        $xml = '<?xml version="1.0" encoding="gbk"?>' . "\n";
        $xml .= '<request><user><name>test-name</name><age>20</age></user><content>hi, test content.</content></request>' . "\n";
        $this->assertEquals($xml, $request->getBody()->getContents());
    }

    /**
     * @param Request $request
     * @depends testFormatEncodingIsGBK
     */
    public function testFormattedEncodingIsGBKContentTypeIs_application_json(Request $request)
    {
        $this->assertEquals('application/xml; charset=gbk', $request->getHeaderLine('content-type'));
    }

}