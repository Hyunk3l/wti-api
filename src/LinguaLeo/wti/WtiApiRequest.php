<?php

namespace LinguaLeo\wti;

use LinguaLeo\wti\Exception\WtiApiException;

/**
 * Class WtiApiRequest
 * @package LinguaLeo\wti
 */
class WtiApiRequest
{
    /**
     * Valid Header parts.
     *
     * @var integer
     */
    const VALID_HEADER_PARTS = 3;

    /**
     * @var
     */
    private $resource;

    /**
     * Error.
     *
     * @var string
     */
    private $error;

    /**
     * Error number.
     *
     * @var integer
     */
    private $errno;

    /**
     * Api result.
     *
     * @var mixed.
     */
    private $result;

    /**
     * Headers.
     *
     * @var mixed.
     */
    private $headers;

    /**
     * Is request done.
     *
     * @var boolean
     */
    private $isRequestDone = false;

    /**
     * Class constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Run.
     */
    public function run()
    {
        $this->isRequestDone = true;
        $result = curl_exec($this->resource);

        $header_size = curl_getinfo($this->resource, CURLINFO_HEADER_SIZE);
        $this->headers = $this->prepareHeaders(substr($result, 0, $header_size));
        $this->result = substr($result, $header_size);

        if ($this->result === false) {
            $this->error = curl_error($this->resource);
            $this->errno = curl_errno($this->resource);
        }
    }

    /**
     * Prepare Headers.
     *
     * @param string $headers_string
     * @return array
     */
    private function prepareHeaders($headers_string)
    {
        $headers = explode(PHP_EOL, $headers_string);
        $headersArray = $this->removeHttpCode($headers);
        $associative_headers = array();

        foreach ($headersArray as $header) {
            if (empty($header)) {
                continue;
            }
            preg_match('~^([^:]*)\:(.*)$~', $header, $matches);
            if (static::VALID_HEADER_PARTS === count($matches)) {
                $associative_headers[$matches[1]] = trim($matches[2]);
            }
        }

        return $associative_headers;
    }

    /**
     * Remove first element of the header array: http code.
     *
     * @param array $headers
     * @return array
     */
    private function removeHttpCode(array $headers)
    {
        array_shift($headers);

        return $headers;
    }

    /**
     * Get error.
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get Error number.
     *
     * @return mixed
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * Get Raw result.
     *
     * @return mixed
     * @throws WtiApiException
     */
    public function getRawResult()
    {
        if (!$this->isRequestDone) {
            throw new WtiApiException("Request must be performed before getting results.");
        }

        return $this->result;
    }

    /**
     * Get result.
     *
     * @param boolean $assoc
     * @return mixed|null
     * @throws WtiApiException
     */
    public function getResult($assoc = false)
    {
        if (!$this->isRequestDone) {
            throw new WtiApiException("Request must be performed before getting results.");
        }

        return $this->result ? json_decode($this->result, $assoc) : null;
    }

    /**
     * Get Headers.
     *
     * @return mixed
     * @throws WtiApiException
     */
    public function getHeaders()
    {
        if (!$this->isRequestDone) {
            throw new WtiApiException("Request must be performed before getting results.");
        }

        return $this->headers;
    }

} 