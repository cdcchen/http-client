<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/10
 * Time: 03:52
 */

namespace cdcchen\curl;

use cdcchen\psr7\HeaderCollection;
use CURLFile;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpRequest
 * @package cdcchen\curl
 */
class HttpClient extends CurlClient
{
    use ClientTrait;

    /**
     * @var string
     */
    private $_format = Formatter::FORMAT_URLENCODED;
    /**
     * @var array|mixed
     */
    private $_data;
    /**
     * @var array
     */
    private $_files = [];

    /**
     * @param RequestInterface $request
     * @return HttpResponse|ResponseInterface
     */
    public function request(RequestInterface $request): HttpResponse
    {
        $method = $request->getMethod();
        $this->setMethod($method);
        $this->addOption(CURLOPT_URL, (string)$request->getUri());

        $statusLine = '';
        $headers = new HeaderCollection();
        $this->setHeaderOutput($statusLine, $headers);

        if ($method === 'GET') {
            $this->addOption(CURLOPT_NOBODY, false);
        } elseif (in_array($method, ['HEAD', 'OPTIONS'])) {
            $this->addOption(CURLOPT_NOBODY, true);
        } elseif ($this->_files) {
            $this->addOption(CURLOPT_POSTFIELDS, array_merge((array)$this->_data, $this->_files));
        }
        if ($this->_data) {
            $request = Formatter::getFormatter($this->_format)->format($this, $request);
        }
        if ($request->getBody()->getSize() > 0) {
            $this->addOption(CURLOPT_POSTFIELDS, $request->getBody()->getContents());
        }

        $this->addOption(CURLOPT_HTTPHEADER, $this->getHeaderLines($request->getHeaders()));
        $response = $this->send();

        $parts = explode(' ', $statusLine, 3);
        $protocol = substr($parts[0], 5);

        return new HttpResponse($parts[1], $headers, $response, $protocol, $parts[2] ?? null);
    }


    /**
     * @param array $headers
     * @return array
     */
    private function getHeaderLines(array $headers): array
    {
        $lines = [];
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'set-cookie') {
                $cookies = array_map(function ($item) {
                    return 'Set-Cookie: ' . $item;
                }, $value);
                $lines = array_merge($lines, $cookies);
            } else {
                $lines[] = $name . ': ' . implode(', ', $value);
            }
        }

        return $lines;
    }

    /**
     * @param string $method
     * @return HttpClient
     */
    private function setMethod(string $method): self
    {
        if ($method === 'POST') {
            $this->addOption(CURLOPT_POST, true);
        } else {
            $this->addOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        return $this;
    }

    /**
     * prepare http request
     */
    protected function prepare(): void
    {

    }

    /**
     * @param string $statusLine
     * @param HeaderCollection $headers
     */
    private function setHeaderOutput(string &$statusLine, HeaderCollection &$headers): void
    {
        $this->addOption(CURLOPT_HEADERFUNCTION,
            function ($handle, string $headerString) use (&$statusLine, &$headers) {
                $header = trim($headerString, "\r\n");
                if (strlen($header) > 0) {
                    if (strpos($header, 'HTTP/') === 0) {
                        $statusLine = $header;
                    } else {
                        $headers->add(...$this->parseHeaderLine($header));
                    }
                }
                return mb_strlen($headerString, '8bit');
            }
        );
    }


    /**
     * @param string $header
     * @return array
     */
    private function parseHeaderLine(string $header): array
    {
        if (($separatorPos = strpos($header, ':')) !== false) {
            $name = strtolower(trim(substr($header, 0, $separatorPos)));
            $value = trim(substr($header, $separatorPos + 1));
            return [$name, $value];
        } else {
            return ['raw', $header];
        }
    }


    /**
     * @param int $value
     * @param bool $ms
     * @return static
     */
    public function setConnectTimeout(int $value, $ms = false): self
    {
        return $this->addOption($ms ? CURLOPT_CONNECTTIMEOUT_MS : CURLOPT_CONNECTTIMEOUT, $value);
    }

    /**
     * @param int $value
     * @param bool $ms
     * @return static
     */
    public function setTimeout(int $value, $ms = false): self
    {
        return $this->addOption($ms ? CURLOPT_TIMEOUT_MS : CURLOPT_TIMEOUT, $value);
    }

    /**
     * @param array|mixed $data
     * @param null $format
     * @return static
     */
    public function setData($data, $format = null)
    {
        $this->_data = $data;

        if ($format !== null) {
            if (Formatter::isValidFormat($format)) {
                $this->_format = $format;
            } else {
                throw new \InvalidArgumentException("Format $format is not invalid.");
            }
        }

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param string $inputName
     * @param array|CURLFile[] $files
     * @return static
     */
    public function addFiles(string $inputName, array $files): self
    {
        $count = count($files);
        if ($count === 1) {
            $this->addFile($inputName, current($files));
        } elseif ($count > 1) {
            foreach ($files as $index => $file) {
                $this->addFile("{$inputName}[{$index}]", $file);
            }
        }

        return $this;
    }

    /**
     * @param string $inputName
     * @param CURLFile $file
     * @return static
     */
    public function addFile(string $inputName, $file): self
    {
        if (is_string($file)) {
            $file = new CURLFile($file);
        } elseif (!($file instanceof $file)) {
            throw new \InvalidArgumentException('Argument $file is must be a valid filename or instance of CURLFile.');
        }

        $this->_files[$inputName] = $file;
        return $this;
    }

    /**
     * @return static
     */
    public function clearFiles(): self
    {
        $this->_files = [];
        return $this;
    }
}