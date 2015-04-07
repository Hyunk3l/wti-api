<?php

namespace LinguaLeo\wti;

/**
 * Class WtiRequestBuilderTest
 * @package LinguaLeo\wti
 */
class WtiRequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Instance of WtiRequestBuilder.
     *
     * @var WtiRequestBuilder
     */
    private $wti_request_builder;

    /**
     * Execute common code before each method.
     */
    public function setUp()
    {
        $fake_api_key = "FAKE";
        $curl_resource = curl_init();
        $this->wti_request_builder = new WtiRequestBuilder($fake_api_key, $curl_resource);
    }

    /**
     * Test endpoint property.
     */
    public function testEndpoint()
    {
        $expected_endpoint = "testendpoint";
        $this->wti_request_builder->setEndpoint($expected_endpoint);
        $this->assertEquals($expected_endpoint, $this->wti_request_builder->getEndpoint(), 'The result is not the expected one.');
    }

    /**
     * Test build method.
     */
    public function testBuild()
    {
        $wti_request = $this->wti_request_builder->build();
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }

    /**
     * Test build using GET method.
     */
    public function testBuildUsingGetMethod()
    {
        $wti_request = $this->wti_request_builder
            ->setMethod('GET')
            ->build()
        ;
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }

    /**
     * Test build without encoding params.
     */
    public function testBuildWithoutUsingJsonEncodedParams()
    {
        $wti_request = $this->wti_request_builder
            ->setJsonEncodeParams(false)
            ->build()
        ;
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }

    /**
     * Test build POST request without encoding params.
     */
    public function testBuildPostRequestWithoutEncodingParams()
    {
        $wti_request = $this->wti_request_builder
            ->setParams(array('file' => 'file/path'))
            ->setJsonEncodeParams(false)
            ->build()
        ;
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }

    /**
     * Test URL using Endpoint.
     */
    public function testBuildRequestUsingEndpoint()
    {
        $params = array(
            'param1'    => 'abc',
            'param2'    => 'def',
        );
        $wti_request = $this->wti_request_builder
            ->setParams($params)
            ->setEndpoint('json')
            ->setMethod('GET')
            ->build()
        ;
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }

    /**
     * Test build request without json endpoint.
     */
    public function testBuildRequestWithoutJsonEndpoint()
    {
        $wti_request = $this->wti_request_builder
            ->setEndpoint('json')
            ->setIsJsonToEndpointAdded(false)
            ->setMethod('GET')
            ->build()
        ;
        $this->assertInstanceOf('Lingualeo\wti\WtiApiRequest', $wti_request);
    }
}
