<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/11
 * Time: 17:48
 */

namespace cdcchen\curl;


use cdcchen\psr7\StreamHelper;
use DOMDocument;
use DOMElement;
use DOMText;
use Psr\Http\Message\RequestInterface;
use SimpleXMLElement;

/**
 * Class XmlFormatter
 * @package cdcchen\curl
 */
class XmlFormatter implements FormatterInterface
{
    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/xml';
    /**
     * @var string the XML version
     */
    public $version = '1.0';
    /**
     * @var string the XML encoding.
     */
    public $encoding = 'utf-8';
    /**
     * @var string the name of the root element.
     */
    public $rootTag = 'request';

    /**
     * @var string
     */
    public $itemTag = 'item';

    /**
     * @inheritdoc
     */
    public function format(HttpClient $client, RequestInterface $request): RequestInterface
    {
        $contentType = $this->contentType;
        $charset = strtolower($this->encoding);
        if (stripos($contentType, 'charset') === false && $charset) {
            $contentType .= '; charset=' . $charset;
        }
        $data = $client->getData();
        if ($data !== null) {
            if ($data instanceof DOMDocument) {
                $content = $data->saveXML();
            } elseif ($data instanceof SimpleXMLElement) {
                $content = $data->saveXML();
            } else {
                $dom = new DOMDocument($this->version, $charset);
                $root = new DOMElement($this->rootTag);
                $dom->appendChild($root);
                $this->buildXml($root, $data);
                $content = $dom->saveXML();
            }

            return $request->withHeader('Content-Type', $contentType)
                           ->withBody(StreamHelper::createStream($content));
        }

        return $request;
    }

    /**
     * @param DOMElement $element
     * @param mixed $data
     */
    protected function buildXml($element, $data)
    {
        if (is_object($data)) {
            $child = new DOMElement(static::pathBasename(get_class($data)));
            $element->appendChild($child);

            $array = [];
            foreach ($data as $name => $value) {
                $array[$name] = $value;
            }

            $this->buildXml($child, $array);

        } elseif (is_array($data)) {
            foreach ($data as $name => $value) {
                if (is_int($name) && is_object($value)) {
                    $this->buildXml($element, $value);
                } elseif (is_array($value) || is_object($value)) {
                    $child = new DOMElement(is_int($name) ? $this->itemTag : $name);
                    $element->appendChild($child);
                    $this->buildXml($child, $value);
                } else {
                    $child = new DOMElement(is_int($name) ? $this->itemTag : $name);
                    $element->appendChild($child);
                    $child->appendChild(new DOMText((string)$value));
                }
            }
        } else {
            $element->appendChild(new DOMText((string)$data));
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private static function pathBasename($path)
    {
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }
        return $path;
    }
}