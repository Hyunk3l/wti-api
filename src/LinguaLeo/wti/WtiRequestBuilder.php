<?php

namespace LinguaLeo\wti;

/**
 * Class WtiRequestBuilder
 * @package LinguaLeo\wti
 */
class WtiRequestBuilder
{
    /**
     * WTI API URL.
     *
     * @var string
     */
    const API_URL = "https://webtranslateit.com/api";

    /**
     * API Key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Api endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * Request method.
     *
     * @var string
     */
    private $method;

    /**
     * Request parameters.
     *
     * @var array
     */
    private $params = array();

    /**
     * API Resource.
     *
     * @var Resource
     */
    private $resource;

    /**
     * Json encode parameters.
     *
     * @var boolean
     */
    private $jsonEncodeParams = true;

    /**
     * If json has been added to endpoint.
     *
     * @var boolean
     */
    private $isJsonToEndpointAdded = true;

    /**
     * Class constructor.
     *
     * @param string $apiKey API Key.
     * @param Resource $resource API Resource.
     */
    public function __construct($apiKey, $resource)
    {
        $this->apiKey = $apiKey;
        $this->resource = $resource;
    }

    /**
     * Request builder.
     *
     * @return WtiApiRequest
     */
    public function build()
    {
        if (RequestMethod::GET !== $this->getMethod()) {
            $params = $this->jsonEncodeParams ? json_encode($this->getParams()) : $this->getParams();
            curl_setopt($this->resource, CURLOPT_POST, true);
            curl_setopt($this->resource, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($this->resource, CURLOPT_POST, false);
            curl_setopt($this->resource, CURLOPT_POSTFIELDS, array());
        }

        curl_setopt($this->resource, CURLOPT_URL, $this->buildRequestUrl());
        curl_setopt($this->resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->resource, CURLOPT_HEADER, 1);
        curl_setopt($this->resource, CURLOPT_CUSTOMREQUEST, $this->getMethod());
        if ($this->isJsonEncodeParams()) {
            curl_setopt($this->resource, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        } else {
            if (RequestMethod::GET !== $this->getMethod()) {
                if (isset($params['file'])) {
                    curl_setopt($this->getResource(), CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
                } else {
                    curl_setopt($this->getResource(), CURLOPT_HTTPHEADER,
                        array('Content-Type: application/x-www-form-urlencoded'));
                }
            }
        }
        return new WtiApiRequest($this->resource);
    }

    /**
     * Build request url.
     *
     * @return string
     */
    private function buildRequestUrl()
    {
        $requestUrl = self::API_URL . "/projects/" . $this->getApiKey();
        if (null !== $this->getEndpoint()) {
            $requestUrl .= "/" . $this->endpoint;
        }
        if ($this->getMethod() === RequestMethod::GET) {
            $requestUrl .= $this->isJsonToEndpointAdded ? ".json" : "";
            if ($this->getParams()) {
                $requestUrl .= "?" . $this->buildUrlParams();
            }
        }
        return $requestUrl;
    }

    /**
     * Build URL parameters.
     *
     * @return array|string
     */
    private function buildUrlParams()
    {
        $params = array_filter($this->params, function ($e) {
            return !is_null($e);
        });

        return $params ? http_build_query($params) : array();
    }

    /**
     * Set endpoint.
     *
     * @param string $endpoint API endpoint.
     * @return WtiRequestBuilder.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Set method.
     *
     * @param string $method Api request method.
     * @return WtiRequestBuilder
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Set parameters.
     *
     * @param array $params Parameters array.
     * @return WtiRequestBuilder
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set json encode parameters.
     *
     * @param boolean $flag
     * @return WtiRequestBuilder
     */
    public function setJsonEncodeParams($flag)
    {
        $this->jsonEncodeParams = $flag;
        return $this;
    }

    /**
     * Set is json to endpoint added.
     *
     * @param boolean $flag
     * @return WtiRequestBuilder
     */
    public function setIsJsonToEndpointAdded($flag)
    {
        $this->isJsonToEndpointAdded = $flag;
        return $this;
    }

    /**
     * Get Api key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get resource.
     *
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get is json encode params.
     *
     * @return boolean
     */
    public function isJsonEncodeParams()
    {
        return $this->jsonEncodeParams;
    }

    /**
     * Get endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
} 
