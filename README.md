# CURL Http Client

## composer 安装

```
composer require cdcchen/curl-client:^1.0.0
```

## 使用教程

### 第一步：实例化`HttpRequest`

```php
$request = new HttpRequest();
$request->setUrl('http://www.baidu.com')
```

### 第二步：发起请求

```php
$response = $request->send();
```

### 第三步：获取返回相关数据

返回的`$response`类型为`HttpResponse`。`HttpResponse`会自动根据返回的`Content-Type`解析返回的数据。目前支持以下格式：

- HttpRequest::FORMAT_JSON
- HttpRequest::FORMAT_XML
- HttpRequest::FORMAT_URLENCODED
- HttpRequest::FORMAT_RAW_URLENCODED

如果已经明确知道返回数据的格式，也可以显式的指定`$response`的format属性：

```php
$response->setFormat(HttpRequest::FORMAT_JSON);
```

然后可以直接通过`$response->getData()`来获取到解析之后的数据。

#### 获取 Status Code

```php
$response->getStatus()
```

#### 获取原始 body 数据：

```php
$response->getContent();
```

#### 获取返回的 Headers

```php
$response->hasHeader($name);
$response->getHeader($name);
$response->getHeaders();
```

#### 获取 Cookies

```php
$response->getCookies();
```


## 设置超时时间

```php
$request->setConnectTimeout($value, $ms = false);
$request->setTimeout($value, $ms = false);
```


## 设置 HttpRequest 请求

### 设置请求方法

```php
$request->setMethod('post');
```

### 设置请求参数

```php
$request->setData(array $data);
```

### 设置post body

```php
$request->setContent($content);
```

### 设置Header

```php
$request->addHeader($name, $value);
$request->addHeaders($headers);
$request->removeHeader($name);
$request->clearHeaders();
```

### Header shortcut

```
$request->setReferrer($referrer = true);
$request->setBasicAuth($username, $password);
$request->setUserPassword($username, $password);
$request->setUserAgent($agent);
$request->setFollowLocation($value = true, $maxRedirects = 5);
$request->setVersion($version);
$request->setAcceptEncoding($value);
```

### 设置Cookies

```php
$request->addCookie($name, $value);
$request->addCookies($cookies);
$request->setCookieFile($file, $jar = null);
```

### 使用SSL

```php
$reqeust->setSSL($peer = false, $host = 2, array $extraOptions = []);
```

### 上传文件

```php
$request->addFile($input_name, $file, $mime_type = null, $post_name = null);
$request->addFiles($input_name, array $files, $mime_type = null, $post_name = null);
$request->clearFiles();
```

> $file 可以为文件路径，也可以为CURLFile实例。

### 设置发起请求时body的格式化方式

```php
$request->setFormat($value);
```

`request`会自动根据format的值来格式化`data`

`format`的取值如下：

- HttpRequest::FORMAT_URLENCODED 默认
- HttpRequest::FORMAT_RAW_URLENCODED
- HttpRequest::FORMAT_JSON
- HttpRequest::FORMAT_XML


## 配置 Curl Options

```php
$request->addOption($name, $value);
$request->addOptions(array $options);
$request->removeOptions($options);
$request->clearOptions();
$request->resetOptions($setDefaultOptions = true);
```