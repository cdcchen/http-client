<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/9/20
 * Time: 19:29
 */

namespace cdcchen\curl;


/**
 * Class TransferInfo
 * @package cdcchen\curl
 */
class TransferInfo
{
    /**
     * @var array
     */
    private $info = [];

    /**
     * TransferInfo constructor.
     * @param resource $handle
     */
    public function __construct($handle)
    {
        $this->info = curl_getinfo($handle);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->info;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->info['url'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getContentType(): ?string
    {
        return $this->info['content_type'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getHttpCode(): ?int
    {
        return $this->info['http_code'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getFileTime(): ?int
    {
        return $this->info['filetime'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getTotalTime(): ?float
    {
        return $this->info['total_time'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getNameLookupTime(): ?float
    {
        return $this->info['namelookup_time'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getConnectTime(): ?float
    {
        return $this->info['connect_time'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getPreTransferTime(): ?float
    {
        return $this->info['pretransfer_time'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getStartTransferTime(): ?float
    {
        return $this->info['starttransfer_time'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getRedirectTime(): ?float
    {
        return $this->info['redirect_time'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getRedirectCount(): ?int
    {
        return $this->info['redirect_count'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getRedirectUrl(): ?string
    {
        return $this->info['redirect_url'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getUploadSize(): ?float
    {
        return $this->info['size_upload'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getDownloadSize(): ?float
    {
        return $this->info['size_download'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getDownloadSpeed(): ?float
    {
        return $this->info['speed_download'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getUploadSpeed(): ?float
    {
        return $this->info['speed_upload'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getHeaderSize(): ?int
    {
        return $this->info['header_size'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getHeaderOut(): ?string
    {
        return $this->info['header_out'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getRequestSize(): ?int
    {
        return $this->info['request_size'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getSSLVerifyResult(): ?string
    {
        return $this->info['ssl_verify_result'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getDownloadContentLength(): ?float
    {
        return $this->info['download_content_length'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getUploadContentLength(): ?float
    {
        return $this->info['upload_content_length'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getPrimaryIP(): ?string
    {
        return $this->info['primary_ip'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getPrimaryPort(): ?int
    {
        return $this->info['primary_port'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getLocalIP(): ?string
    {
        return $this->info['local_ip'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getLocalPort(): ?int
    {
        return $this->info['local_port'] ?? null;
    }

    /**
     * @return array|null
     */
    public function getCertInfo(): ?array
    {
        return $this->info['certinfo'] ?? null;
    }
}