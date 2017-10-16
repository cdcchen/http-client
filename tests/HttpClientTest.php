<?php

use cdcchen\curl\HttpClient;
use cdcchen\curl\HttpResponse;
use cdcchen\psr7\Request;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testInitializable()
    {
        $this->assertInstanceOf(HttpClient::class, new HttpClient());
    }

    public function testGetRequestShouldReturnPsr7HttpResponse()
    {
        $client = new HttpClient();
        $url = 'http://127.0.0.1:9090/tests/response.php';
        $request = new Request('GET', $url);

        $this->assertInstanceOf(HttpResponse::class, $client->request($request));
    }

    public function testPostRequestShouldReturnPsr7HttpResponse()
    {
        $client = new HttpClient();
        $url = 'http://127.0.0.1:9090/tests/response.php';
        $request = new Request('POST', $url);

        $this->assertInstanceOf(HttpResponse::class, $client->request($request));
    }

    public function testStaticGetRequestShouldReturnPsr7HttpResponse()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php?action=user';
        $response = HttpClient::get($url, ['username' => 'cdcchen']);
        $this->assertInstanceOf(HttpResponse::class, $response);
    }

    public function testStaticPostRequestShouldReturnPsr7HttpResponse()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php/test.php?action=user';
        $response = HttpClient::post($url, ['sex' => 'male']);

        $this->assertInstanceOf(HttpResponse::class, $response);
    }

    public function testStaticUploadFileRequestShouldReturnPsr7HttpResponse()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php/test.php?action=user';
        $files = [
            'file1' => [
                realpath(__FILE__),
                realpath(__FILE__),
                new \CURLFile(realpath(__FILE__)),
            ],
        ];
        $response = HttpClient::upload($url, $files);

        $this->assertInstanceOf(HttpResponse::class, $response);
    }
}
