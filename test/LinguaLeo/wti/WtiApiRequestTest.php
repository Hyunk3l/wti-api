<?php

namespace LinguaLeo\wti;

/**
 * Class WtiApiRequestTest
 * @package LinguaLeo\wti
 */
class WtiApiRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Instance of WtiApiRequest.
	 *
	 * @var WtiApiRequest
	 */
	private $wti_api_request;

	/**
	 *
	 */
	public function setUp()
	{
		$resource = new StdClass;

		$this->wti_api_request = new WtiApiRequest($resource);
	}
}
