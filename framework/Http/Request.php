<?php

namespace Framework\Http;

class Request
{
    private $swoole_http_request;

    public function __construct(\swoole_http_request $swoole_http_request)
    {
        $this->swoole_http_request  = $swoole_http_request;
    }

    private function getSwooleHttpRequest()
    {
        return $this->swoole_http_request;
    }

    public function method()
    {
        return $this->swoole_http_request->server['request_method'];
    }

    public function schema()
    {
        return $this->swoole_http_request->server['server_protocol'];
    }

    public function host()
    {
        return $this->swoole_http_request->header['host'];
    }

    public function uri()
    {
        return $this->swoole_http_request->server['request_uri'];
    }

    public function fullUrl()
    {
        $schema_string = 'http://';
        if (strripos($this->schema(), 'HTTP/')) {
            $schema_string = 'http://';
        } else {
            $schema_string = 'https://';
        }

        return $schema_string.$this->host().$this->uri().($this->queryString() ? '?'.$this->queryString() : '');
    }

    public function contentType()
    {
        $contentType = $this->swoole_http_request->header['content-type'] ?? '';
        if (stripos($contentType, 'multipart/form-data')) {
            return 'multipart/form-data';
        }
        return $contentType;
    }

    public function queryString()
    {
        return $this->swoole_http_request->server['query_string'] ?? '';
    }

    public function query()
    {
        $result = [];
        $items = explode('&', $this->queryString());
        if (count($items) == 2) {
            foreach ($items as $item) {
                $kv = explode('=', $item);
                $result[] = [$kv[0] => $kv[1]]; 
            }
        }
        
        return $result;
    }

    public function body()
    {
        if ($this->contentType() == 'application/json') {
            return json_decode($request->rawContent(), true);
        }
        if ($this->contentType() == 'multipart/form-data' or $this->contentType() == 'application/x-www-form-urlencoded') {
            return $this->body;
        }
        return [];
    }

    public function all()
    {
        return array_merge($this->query(), $this->body());
    } 

    public function input($key, $value = null)
    {
        if (array_key_exists($key, $this->all())) {
            $value = $this->all()[$key];
        }
        return $value;
    }
}