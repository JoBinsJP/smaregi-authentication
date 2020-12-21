<?php

namespace App\Infrastructure\Libraries;

use Arr;
use GuzzleHttp\RequestOptions;

/**
 * Class HttpRequestOption
 * @package App\Infrastructure\Libraries\HttpRequest
 */
class HttpRequestOption
{
    /**
     * @var string
     */
    protected $method = 'GET';
    /**
     * @var string
     */
    protected $url = '';
    /**
     * @var string
     */
    protected $token = '';
    /**
     * @var array
     */
    protected $formData = [];
    /**
     * @var string
     */
    protected $contentType;
    /**
     * @var bool
     */
    protected $isJson = false;
    /**
     * @var array
     */
    protected $query;

    /**
     * @param string|null $url
     *
     * @return string
     */
    public function url(?string $url = null): string
    {
        if ( $url ) {
            $this->url = $url;
        }

        return $this->url;
    }

    /**
     * @param string|null $method
     *
     * @return string
     */
    public function method(?string $method = null): string
    {
        if ( $method ) {
            $this->method = strtoupper($method);
        }

        return $this->method;
    }

    /**
     * @param array|string|null $token
     * @param string            $type
     *
     * @return string
     */
    public function token($token, string $type = 'Bearer'): string
    {

        if ( !$token ) {
            return $this->token;
        }

        if ( $type === 'Basic' ) {
            $clientId     = Arr::get($token, 'client_id');
            $clientSecret = Arr::get($token, 'client_secret');
            $token        = base64_encode("{$clientId}:{$clientSecret}");
        }

        return $this->token = "{$type} ${token}";
    }

    /**
     * @param string|null $contentType
     *
     * @return string
     */
    public function contentType(?string $contentType = null): string
    {
        if ( $contentType ) {
            $this->contentType = $contentType;
        }

        if ( $this->contentType ) {
            return $this->contentType;
        }

        if ( $this->isJson ) {
            return 'application/json';
        }

        if ( $this->method === 'POST' && !empty($this->formData) ) {
            return 'application/x-www-form-urlencoded';
        }

        return '';
    }

    /**
     * @param array $data
     * @param bool  $isJson
     *
     * @return array
     */
    public function formData(array $data = [], bool $isJson = false): array
    {
        if ( !empty($data) ) {
            $this->formData = $data;
            $this->isJson   = $isJson;
        }

        return $this->formData;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function query(array $data = []): array
    {
        if ( !empty($data) ) {
            $this->query = $data;
        }

        return $this->query ?: [];
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        $headers = [];
        if ( $this->token ) {
            $headers['Authorization'] = $this->token;
        }

        if ( $this->contentType() ) {
            $headers['Content-Type'] = $this->contentType();
        }

        return $headers;
    }

    /**
     * @return array
     */
    public function httpOptions(): array
    {
        $options = [];

        $headers = $this->headers();
        if ( !empty($headers) ) {
            $options['headers'] = $headers;
        }

        if ( $this->method === 'POST' && !empty($this->formData) ) {
            if ( $this->isJson ) {
                $options[RequestOptions::JSON] = $this->formData;
            } else {
                $options['form_params'] = $this->formData;
            }
        }

        if ( $this->method === 'GET' && !empty($this->query) ) {
            $options['query'] = $this->query;
        }

        return $options;
    }
}
