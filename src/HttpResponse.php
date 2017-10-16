<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 04:41
 */

namespace cdcchen\curl;

use cdcchen\psr7\Response;


/**
 * Class HttpResponse
 * @package cdcchen\curl
 */
class HttpResponse extends Response
{
    /**
     *
     */
    const STATUS_HEADER_NAME = 'http-code';

    /**
     * @var array cookies.
     */
    private $_cookies;

    /**
     * @var mixed content data
     */
    private $_data;

    /**
     * @var string content format name
     */
    private $_format;


    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->_data === null) {
            $content = $this->getBody()->getContents();
            if (!empty($content)) {
                $data = $this->getParser()->parse($this);
                $this->setData($data);
            }
        }
        return $this->_data;
    }

    /**
     * @return bool
     */
    public function isOK()
    {
        return $this->getStatusCode() == 200;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        $status = $this->getStatusCode();
        return $status >= 200 && $status < 300;
    }

    /**
     * Sets body format.
     * @param string $format body format name.
     * @return $this self reference.
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Returns body format.
     * @return string body format name.
     */
    public function getFormat()
    {
        if ($this->_format === null) {
            $this->_format = $this->defaultFormat();
        }
        return $this->_format;
    }

    /**
     * Returns HTTP message parser instance for the specified format.
     * @return ParserInterface parser instance.
     * @throws \InvalidArgumentException on invalid format name.
     */
    private function getParser()
    {
        static $defaultParsers = [
            Formatter::FORMAT_JSON => 'cdcchen\curl\JsonParser',
            Formatter::FORMAT_URLENCODED => 'cdcchen\curl\UrlEncodedParser',
            Formatter::FORMAT_RAW_URLENCODED => 'cdcchen\curl\UrlEncodedParser',
            Formatter::FORMAT_XML => 'cdcchen\curl\XmlParser',
        ];

        if (!isset($defaultParsers[$this->getFormat()])) {
            throw new \InvalidArgumentException("Unrecognized format '{$this->getFormat()}'");
        }
        $parser = $defaultParsers[$this->getFormat()];

        if (!is_object($parser)) {
            $parser = new $parser;
        }

        return $parser;
    }

    /**
     * Returns default format automatically detected from headers and content.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function defaultFormat()
    {
        $format = $this->detectFormatByHeaders($this->getHeaders());
        if ($format === null) {
            $format = $this->detectFormatByContent($this->getBody()->getContents());
        }
        return $format;
    }

    /**
     * Detects format from headers.
     * @param array $headers source headers.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByHeaders(array $headers)
    {
        $contentType = $headers['content-type'];
        if (!empty($contentType)) {
            if (stripos($contentType, 'json') !== false) {
                return Formatter::FORMAT_JSON;
            }
            if (stripos($contentType, 'urlencoded') !== false) {
                return Formatter::FORMAT_URLENCODED;
            }
            if (stripos($contentType, 'xml') !== false) {
                return Formatter::FORMAT_XML;
            }
        }
        return null;
    }

    /**
     * Detects response format from raw content.
     * @param string $content raw response content.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByContent($content)
    {
        if (preg_match('/^\\{.*\\}$/is', $content)) {
            return Formatter::FORMAT_JSON;
        }
        if (preg_match('/^[^=|^&]+=[^=|^&]+(&[^=|^&]+=[^=|^&]+)*$/', $content)) {
            return Formatter::FORMAT_URLENCODED;
        }
        if (preg_match('/^<.*>$/s', $content)) {
            return Formatter::FORMAT_XML;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getCookies()
    {
        if (count($this->_cookies) === 0 && $this->hasHeader('set-cookie')) {
            $cookieStrings = $this->getHeader('set-cookie');
            foreach ((array)$cookieStrings as $cookieString) {
                $cookie = $this->parseCookie($cookieString);
                $this->_cookies[$cookie['name']] = $cookie;
            }
        }
        return $this->_cookies;
    }

    /**
     * Parses cookie value string, creating a [[Cookie]] instance.
     * @param string $cookieString cookie header string.
     * @return array cookie array.
     */
    private function parseCookie($cookieString)
    {
        $params = [];
        $pairs = explode(';', $cookieString);
        foreach ($pairs as $number => $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') === false) {
                $params[$this->normalizeCookieParamName($pair)] = true;
            } else {
                list($name, $value) = explode('=', $pair, 2);
                if ($number === 0) {
                    $params['name'] = $name;
                    $params['value'] = urldecode($value);
                } else {
                    $params[$this->normalizeCookieParamName($name)] = urldecode($value);
                }
            }
        }
        return $params;
    }

    /**
     * @param string $rawName raw cookie parameter name.
     * @return string name of [[Cookie]] field.
     */
    private function normalizeCookieParamName($rawName)
    {
        static $nameMap = [
            'expires' => 'expire',
            'httponly' => 'httpOnly',
            'max-age' => 'maxAge',
        ];
        $name = strtolower($rawName);
        if (isset($nameMap[$name])) {
            $name = $nameMap[$name];
        }
        return $name;
    }

}