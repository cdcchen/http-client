<?php

use cdcchen\curl\HttpClient;
use cdcchen\curl\HttpResponse;
use PHPUnit\Framework\TestCase;

final class ClientTraitTest extends TestCase
{
    public function testStaticGetRequestShouldReturnPsr7HttpResponse()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php';
        $response = HttpClient::get($url, ['username' => 'cdcchen']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticGetRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestMethodIsGet($res)
    {
//        $this->markTestSkipped();
        $this->assertEquals('GET', strtoupper($res['method']));
    }

    public function testStaticPostRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::post($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends  testStaticPostRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsPost($res)
    {
        $this->assertEquals('POST', strtoupper($res['method']));
    }

    public function testStaticPutRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::put($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticPutRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsPut($res)
    {
        $this->assertEquals('PUT', strtoupper($res['method']));
    }

    public function testStaticHeadRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::head($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticHeadRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsHeadAndNotBody($res)
    {
        $this->assertEquals('', $res);
    }

    public function testStaticPatchRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::patch($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticPatchRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsPatch($res)
    {
        $this->assertEquals('PATCH', strtoupper($res['method']));
    }

    public function testStaticOptionsRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::options($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticOptionsRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsOptionsAndNotBody($res)
    {
        $this->assertEquals('', $res);
    }

    public function testStaticDeleteRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::delete($url, ['sex' => 'male']);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticDeleteRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestIsDelete($res)
    {
        $this->assertEquals('DELETE', strtoupper($res['method']));
    }

    public function testStaticUploadFileRequestShouldReturnPsr7HttpResponse()
    {
//        $this->markTestSkipped();
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $files = [
            'file1' => [
                realpath(__FILE__),
                realpath(__FILE__),
                new \CURLFile(realpath(__FILE__)),
            ],
        ];
        $response = HttpClient::upload($url, $files);
        $this->assertInstanceOf(HttpResponse::class, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $res
     * @depends testStaticUploadFileRequestShouldReturnPsr7HttpResponse
     */
    public function testRequestUploadFile($res)
    {
        $this->assertEquals('POST', strtoupper($res['method']));
    }
}
