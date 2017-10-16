<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/5/4
 * Time: 下午5:58
 */

namespace cdcchen\curl;


/**
 * Class Request
 * @package cdcchen\curl
 */
class CurlClient
{
    /**
     * @var OptionCollection
     */
    public $options;
    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var array
     */
    protected static $defaultOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_DNS_USE_GLOBAL_CACHE => true,
        CURLOPT_FORBID_REUSE => true,
    ];
    /**
     * @var string
     */
    private $url;

    /**
     * @var TransferInfo
     */
    private $transferInfo;


    /**
     * Request constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = new OptionCollection();
        $this->addDefaultOptions()->addOptions($options);
    }

    /**
     * @param bool $value
     * @return static
     */
    public function setDebug(bool $value): self
    {
        $this->debug = (bool)$value;
        return $this->addOption(CURLOPT_VERBOSE, $this->debug);
    }

    /**
     * @param array $options
     * @return static
     */
    public function setOptions(array $options): self
    {
        return $this->clearOptions()->addOptions($options);
    }

    /**
     * @return static
     */
    private function addDefaultOptions(): self
    {
        return $this->addOptions(static::$defaultOptions);
    }

    /**
     * @param int $option
     * @param mixed $value
     * @return static
     */
    public function addOption(int $option, $value): self
    {
        $this->options->set($option, $value);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function addOptions(array $options): self
    {
        foreach ($options as $option => $value) {
            $this->options->set($option, $value);
        }

        return $this;
    }

    /**
     * @param int $option
     * @return bool
     */
    public function hasOptions(int $option)
    {
        return $this->options->has($option);
    }

    /**
     * @param int $option
     * @return mixed
     */
    public function getOption(int $option)
    {
        return $this->options->get($option);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options->toArray();
    }

    /**
     * @param int $option
     * @return static
     */
    public function removeOption(int $option): self
    {
        $this->options->remove($option);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function removeOptions(array $options): self
    {
        foreach ($options as $option) {
            $this->options->remove($option);
        }

        return $this;
    }

    /**
     * @param bool $setDefaultOptions
     * @return static
     */
    public function resetOptions(bool $setDefaultOptions = true): self
    {
        $this->clearOptions();
        if ($setDefaultOptions) {
            $this->addOptions(static::$defaultOptions);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function clearOptions(): self
    {
        $this->options->removeAll();
        return $this;
    }

    /**
     * @param string $url
     * @return static
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this->addOption(CURLOPT_URL, $url);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param array $options
     * @param bool $merge
     */
    public static function setDefaultOptions(array $options, bool $merge = false): void
    {
        if ($merge) {
            foreach ($options as $option => $value) {
                static::$defaultOptions[$option] = $value;
            }
        } else {
            static::$defaultOptions = $options;
        }
    }

    /**
     * @param null|int $option
     * @return array|mixed|null
     */
    public static function getDefaultOptions(int $option = null)
    {
        if ($option === null) {
            return static::$defaultOptions;
        }

        return static::$defaultOptions[$option] ?? null;
    }

    /**
     * @param string|null $url
     * @return bool|string
     * @throws RequestException
     */
    public function send(string $url = null)
    {
        $handle = curl_init();
        if (!$this->beforeRequest($this, $handle)) {
            return false;
        }

        $this->prepare();
        $this->addOption(CURLOPT_VERBOSE, $this->debug);

        if ($url !== null) {
            $this->setUrl($url);
        }
        curl_setopt_array($handle, $this->getOptions());

        $content = curl_exec($handle);

        // check cURL error
        $errorNumber = curl_errno($handle);
        $errorMessage = curl_error($handle);
        $this->transferInfo = new TransferInfo($handle);
        $this->afterRequest($this, $handle);
        curl_close($handle);

        if ($errorNumber !== CURLE_OK) {
            throw new RequestException('Curl error: #' . $errorNumber . ' - ' . $errorMessage, $errorNumber);
        }

        return $content;
    }

    /**
     * prepare request params
     */
    protected function prepare(): void
    {
    }

    /**
     * @param array $requests
     */
    public function batchExecute(array $requests)
    {
    }

    /**
     * @param CurlClient $client
     * @param resource $handle curl_init resource
     * @return bool
     */
    protected function beforeRequest(self $client, $handle): bool
    {
        return true;
    }

    /**
     * @param CurlClient $request
     * @param resource $handle
     */
    protected function afterRequest(self $request, $handle)
    {
    }

    /**
     * @return TransferInfo
     */
    public function getTransferInfo()
    {
        return $this->transferInfo;
    }
}