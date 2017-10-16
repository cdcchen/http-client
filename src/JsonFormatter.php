<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/11
 * Time: 15:43
 */

namespace cdcchen\curl;

use cdcchen\psr7\StreamHelper;
use Psr\Http\Message\RequestInterface;


/**
 * Class JsonFormatter
 * @package cdcchen\curl
 */
class JsonFormatter implements FormatterInterface
{
    /**
     * @var integer the encoding options.For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>.
     */
    public $encodeOptions = 320; // JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * @inheritdoc
     */
    public function format(HttpClient $client, RequestInterface $request): RequestInterface
    {
        $stream = StreamHelper::createStream(json_encode($client->getData(), $this->encodeOptions));

        return $request->withHeader('Content-Type', 'application/json; charset=utf-8')
                       ->withBody($stream);
    }
}