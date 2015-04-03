<?php

namespace LinguaLeo\wti;

/**
 * Class WtiApiRequestTest
 * @package LinguaLeo\wti
 */
class WtiApiRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Api fake URL.
     *
     * @var string
     */
    const API_FAKE_URL = 'https://webtranslateit.com/api/projects/fake';

    /**
     * Instance of WtiApiRequest.
     *
     * @var WtiApiRequest
     */
    private $wti_api_request;

    /**
     * Execute common code before each method.
     */
    public function setUp()
    {
        $resource = $this->getFakeCurlResource();
        $this->wti_api_request = new WtiApiRequest($resource);
    }

    /**
     * Get fake cURL resource.
     *
     * @param string $url Resource URL.
     * @return Curl resource.
     */
    private function getFakeCurlResource($url = null)
    {
        $resource = curl_init();
        curl_setopt($resource, CURLOPT_URL, $url);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_HEADER, 1);
        curl_setopt($resource, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($resource, CURLOPT_POST, false);
        curl_setopt($resource, CURLOPT_POSTFIELDS, array());

        return $resource;
    }

    /**
     * Test run with no result.
     */
    public function testRunWithNoResult()
    {
        $this->wti_api_request->run();
        $result = $this->wti_api_request->getResult();
        $this->assertNull($result);
    }

    /**
     * Test run with error.
     */
    public function testRunWithError()
    {
        $this->wti_api_request->run();
        $error = $this->wti_api_request->getError();
        $errno = $this->wti_api_request->getErrno();
        $this->assertEquals(3, $errno);
        $this->assertEquals('<url> malformed', $error);
    }

    /**
     * Test run with results as object.
     */
    public function testRunWithResultsAsObject()
    {
        $resource = $this->getFakeCurlResource(static::API_FAKE_URL);
        $this->wti_api_request = new WtiApiRequest($resource);
        $this->wti_api_request->run();
        $result = $this->wti_api_request->getResult();
        $this->assertTrue(is_object($result));
        $this->assertFalse(is_array($result));
    }

    /**
     * Test run with results as array.
     */
    public function testRunWithResultsAsArray()
    {
        $resource = $this->getFakeCurlResource(static::API_FAKE_URL);
        $this->wti_api_request = new WtiApiRequest($resource);
        $this->wti_api_request->run();
        $result = $this->wti_api_request->getResult(true);
        $this->assertFalse(is_object($result));
        $this->assertTrue(is_array($result));
    }

    /**
     * Test result has headers.
     */
    public function testResultHasHeaders()
    {
        $resource = $this->getFakeCurlResource(static::API_FAKE_URL);
        $this->wti_api_request = new WtiApiRequest($resource);
        $this->wti_api_request->run();
        $headers = $this->wti_api_request->getHeaders();
        $this->assertInternalType('array', $headers);
        $this->assertNotEmpty($headers);
        $this->assertArrayHasKey('Content-Type', $headers);
    }

    /**
     * Test request result is raw.
     */
    public function testResultIsRaw()
    {
        $resource = $this->getFakeCurlResource(static::API_FAKE_URL);
        $this->wti_api_request = new WtiApiRequest($resource);
        $this->wti_api_request->run();
        $raw_result = $this->wti_api_request->getRawResult();
        $this->assertTrue(is_string($raw_result));
    }

    /**
     * Test get result when request is not done.
     *
     * @throws Exception\WtiApiException
     */
    public function testGetResultWhenRequestIsNotDone()
    {
        $this->setExpectedException('LinguaLeo\wti\Exception\WtiApiException');
        $this->wti_api_request->getResult();
    }

    /**
     * Test get raw result when request is not done.
     *
     * @throws Exception\WtiApiException
     */
    public function testGetRawResultWhenRequestIsNotDone()
    {
        $this->setExpectedException('LinguaLeo\wti\Exception\WtiApiException');
        $this->wti_api_request->getRawResult();
    }

    /**
     * Test get headers when request is not done.
     *
     * @throws Exception\WtiApiException
     */
    public function testGetHeadersWhenRequestIsNotDone()
    {
        $this->setExpectedException('LinguaLeo\wti\Exception\WtiApiException');
        $this->wti_api_request->getHeaders();
    }
}
