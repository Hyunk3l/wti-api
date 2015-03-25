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
	 * Is request runned.
	 *
	 * @var boolean
	 */
	private $isRequestRunned = false;

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
		$this->isRequestRunned = true;
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
	 * @param string $headersString
	 * @return array
	 */
	private function prepareHeaders($headersString)
	{
		$headersArray = explode(PHP_EOL, $headersString);
		// Remove first header, HTTP code
		array_shift($headersArray);

		$headersAssoc = array();

		foreach ($headersArray as $header) {
			if ($header === '') {
				continue;
			}
			preg_match('~^([^:]*)\:(.*)$~', $header, $matches);
			if (count($matches) == 3) {
				$headersAssoc[$matches[1]] = trim($matches[2]);
			}
		}

		return $headersAssoc;
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
		if (!$this->isRequestRunned) {
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
		if (!$this->isRequestRunned) {
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
		if (!$this->isRequestRunned) {
			throw new WtiApiException("Request must be performed before getting results.");
		}
		return $this->headers;
	}

} 