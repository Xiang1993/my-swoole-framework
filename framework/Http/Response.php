<?php

namespace Framework\Http;

class Response
{
    private $swoole_http_response;

    private $content;

    public function __construct(\swoole_http_response $swoole_http_response)
    {
        $this->swoole_http_response = $swoole_http_response;
        $this->swoole_http_response->header('content-type', 'text/html');
        $this->swoole_http_response->status(200);
        $this->content = '';
    }

    public function getSwooleHttpResponse()
    {
        return $this->swoole_http_response;
    }

    public function json($data, $status = 200)
    {
        if (!is_null($data)) {
            $this->content = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $this->content = '{}';
        }

        $this->withHeader('content-type', 'application/json');
        $this->swoole_http_response->status($status);
        return $this;
    }

    public function withHeader($key, $value)
    {
        $this->swoole_http_response->header($key, $value);
        return $this;
    }

    public function withStatus($code)
    {
        $this->swoole_http_response->status($code);
        return $this;
    }

    public function send($content = null)
    {
        $this->content = $content ? $content : $this->content;
        $this->swoole_http_response->end($this->content);
    }
}