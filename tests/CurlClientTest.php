<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/20
 * Time: 14:12
 */

use cdcchen\curl\CurlClient;
use PHPUnit\Framework\TestCase;
use cdcchen\curl\TransferInfo;

class CurlClientTest extends TestCase
{
    public function setUp()
    {
        $defaultOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_DNS_USE_GLOBAL_CACHE => true,
            CURLOPT_FORBID_REUSE => true,
        ];
        CurlClient::setDefaultOptions($defaultOptions);
    }

    public function testInit()
    {
        $this->assertInstanceOf(CurlClient::class, new CurlClient());
    }

    public function testSetDebugTrue()
    {
        $client = new CurlClient();
        $client->setDebug(true);
        $this->assertTrue($client->debug);
    }

    public function testSetDebugFalse()
    {
        $client = new CurlClient();
        $client->setDebug(false);
        $this->assertFalse($client->debug);
    }

    public function testClearOptions()
    {
        $client = new CurlClient();
        $client->clearOptions();
        $this->assertEmpty($client->getOptions());
    }

    public function testSetOptions()
    {
        $options = [
            CURLOPT_URL => __FILE__,
            CURLOPT_POSTFIELDS => ['username' => 'chen', 'gender' => 'male', 'age' => 30],
        ];
        $client = new CurlClient();
        $client->setOptions($options);
        $this->assertEquals($options, $client->getOptions());
    }

    public function testAddOption()
    {
        $optionName = CURLOPT_URL;
        $optionValue = __FILE__;
        $client = new CurlClient();
        $client->addOption($optionName, $optionValue);
        $options = $client->getOptions();
        $this->assertTrue(isset($options[$optionName]) && $options[$optionName] === $optionValue);

        return $client;
    }

    public function testHasOptionReturnTrue()
    {
        $client = new CurlClient();
        $client->addOption(CURLOPT_URL, __FILE__);
        $this->assertTrue($client->hasOptions(CURLOPT_URL));

        return $client;
    }


    /**
     * @param CurlClient $client
     * @depends testHasOptionReturnTrue
     */
    public function testHasOptionReturnFalse(CurlClient $client)
    {
        $client->removeOption(CURLOPT_URL);
        $this->assertFalse($client->hasOptions(CURLOPT_URL));
    }

    /**
     * @depends testAddOption
     * @param CurlClient $client
     */
    public function testRemoveOption(CurlClient $client)
    {
        $client->removeOption(CURLOPT_URL);
        $this->assertArrayNotHasKey(CURLOPT_URL, $client->getOptions());
    }

    public function testAddOptions()
    {
        $postFields = ['username' => 'chen', 'gender' => 'male', 'age' => 30];
        $options = [
            CURLOPT_URL => __FILE__,
            CURLOPT_POSTFIELDS => $postFields,
        ];
        $client = new CurlClient();
        $client->addOptions($options);

        $options = $client->getOptions();
        $actual = isset($options[CURLOPT_URL]) && $options[CURLOPT_URL] === __FILE__ &&
            isset($options[CURLOPT_POSTFIELDS]) && $options[CURLOPT_POSTFIELDS] === $postFields;

        $this->assertTrue($actual);

        return $client;
    }

    /**
     * @param CurlClient $client
     * @depends testAddOptions
     */
    public function testRemoveOptions(CurlClient $client)
    {
        $client->removeOptions([CURLOPT_URL, CURLOPT_POSTFIELDS]);
        $options = $client->getOptions();
        $this->assertTrue(!isset($options[CURLOPT_URL]) && !isset($options[CURLOPT_POSTFIELDS]));
    }

    /**
     * @param CurlClient $client
     * @depends testAddOptions
     */
    public function testResetOptionsAndSetDefaultOptions(CurlClient $client)
    {
        $client->resetOptions();
        $this->assertEquals(CurlClient::getDefaultOptions(), $client->getOptions());
    }

    /**
     * @param CurlClient $client
     * @depends testAddOptions
     */
    public function testResetOptionsAndNotSetDefaultOptions(CurlClient $client)
    {
        $client->resetOptions(false);
        $this->assertEmpty($client->getOptions());
    }

    public function testSetUrl()
    {
        $client = new CurlClient();
        $client->setUrl(__FILE__);
        $this->assertEquals($client->getOption(CURLOPT_URL), __FILE__);

        return $client;
    }

    /**
     * @param CurlClient $client
     * @depends testSetUrl
     */
    public function testGetUrl(CurlClient $client)
    {
        $this->assertEquals(__FILE__, $client->getUrl());
    }

    public function testSetDefaultOptionsByMerge()
    {
        $options = [
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 600,
        ];
        $defaultOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_DNS_USE_GLOBAL_CACHE => true,
            CURLOPT_FORBID_REUSE => true,
        ];
        /* @var array $expected */
        $expected = $options + $defaultOptions;
        ksort($expected);
        CurlClient::setDefaultOptions($options, true);
        $actual = CurlClient::getDefaultOptions();
        ksort($actual);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultOptionsByReplace()
    {
        $options = [
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 600,
        ];
        CurlClient::setDefaultOptions($options, false);
        $this->assertEquals($options, CurlClient::getDefaultOptions());
    }

    public function testSendShouldReturnTrue()
    {
        $client = new CurlClient([
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_NOBODY => true,
            CURLOPT_URL => 'http://127.0.0.1:9090/tests/response.php',
        ]);
        $this->assertTrue($client->send());
    }

    public function testSendShouldReturnRawContent()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php';
        $client = new CurlClient();
        $this->assertTrue(is_string($client->send($url)));
    }

    public function testSendShouldThrownRequestException()
    {
        $this->expectException(\cdcchen\curl\RequestException::class);
        $client = new CurlClient();
        $client->send();
    }

    public function testGetTransferInfo()
    {
        $url = 'http://127.0.0.1:9090/tests/response.php';
        $client = new CurlClient();
        $client->send($url);
        $info = $client->getTransferInfo();
        $this->assertInstanceOf(TransferInfo::class, $info);
    }
}