<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 15.11.2017
 * Time: 13:30
 */

use DEX\Hook;
use DEX\IParser;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    /**
     * @expectedException Zend\Http\Client\Adapter\Exception\RuntimeException
     * @expectedExceptionCode 0
     */
    public function testMockBadUrl()
    {

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(false);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);

        $hook->do();
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 1
     */
    public function testMockValid()
    {
        $json = "{\"version\": 0,\"data\": {\"this\": 2,\"that\": 4,\"other\": 1}}";

        $mockResponse = $this->createMock(Zend\Http\Response::class);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($json);

        $mockClient = $this->createMock(Zend\Http\Client::class);
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(false);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);
        $hook_ref = new ReflectionClass($hook);
        $hook_ref_prop = $hook_ref->getProperty('client');
        $hook_ref_prop->setAccessible(true);

        $hook_ref_prop->setValue($hook, $mockClient);

        $hook->do();
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 2
     */
    public function testMockBefore()
    {
        $json = "{\"version\": 0,\"data\": {\"this\": 2,\"that\": 4,\"other\": 1}}";

        $mockResponse = $this->createMock(Zend\Http\Response::class);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($json);

        $mockClient = $this->createMock(Zend\Http\Client::class);
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(true);
        $parser->method('before')
            ->willReturn(false);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);
        $hook_ref = new ReflectionClass($hook);
        $hook_ref_prop = $hook_ref->getProperty('client');
        $hook_ref_prop->setAccessible(true);

        $hook_ref_prop->setValue($hook, $mockClient);

        $hook->do();
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 3
     */
    public function testMockRun()
    {
        $json = "{\"version\": 0,\"data\": {\"this\": 2,\"that\": 4,\"other\": 1}}";

        $mockResponse = $this->createMock(Zend\Http\Response::class);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($json);

        $mockClient = $this->createMock(Zend\Http\Client::class);
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(true);
        $parser->method('before')
            ->willReturn(true);
        $parser->method('run')
            ->willReturn(false);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);
        $hook_ref = new ReflectionClass($hook);
        $hook_ref_prop = $hook_ref->getProperty('client');
        $hook_ref_prop->setAccessible(true);

        $hook_ref_prop->setValue($hook, $mockClient);

        $hook->do();
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 4
     */
    public function testMockAfter()
    {
        $json = "{\"version\": 0,\"data\": {\"this\": 2,\"that\": 4,\"other\": 1}}";

        $mockResponse = $this->createMock(Zend\Http\Response::class);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($json);

        $mockClient = $this->createMock(Zend\Http\Client::class);
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(true);
        $parser->method('before')
            ->willReturn(true);
        $parser->method('run')
            ->willReturn(true);
        $parser->method('after')
            ->willReturn(false);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);
        $hook_ref = new ReflectionClass($hook);
        $hook_ref_prop = $hook_ref->getProperty('client');
        $hook_ref_prop->setAccessible(true);

        $hook_ref_prop->setValue($hook, $mockClient);

        $hook->do();
    }

    public function testMockSuccessfully()
    {
        $json = "{\"version\": 0,\"data\": {\"this\": 2,\"that\": 4,\"other\": 1}}";

        $mockResponse = $this->createMock(Zend\Http\Response::class);
        $mockResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($json);

        $mockClient = $this->createMock(Zend\Http\Client::class);
        $mockClient->expects($this->any())
            ->method('send')
            ->willReturn($mockResponse);

        $parser = $this->createMock(IParser::class);
        $parser->method('validation')
            ->willReturn(true);
        $parser->method('before')
            ->willReturn(true);
        $parser->method('run')
            ->willReturn(true);
        $parser->method('after')
            ->willReturn(true);

        /**
         * @var $parser IParser
         */
        $hook = new Hook($parser, []);
        $hook_ref = new ReflectionClass($hook);
        $hook_ref_prop = $hook_ref->getProperty('client');
        $hook_ref_prop->setAccessible(true);

        $hook_ref_prop->setValue($hook, $mockClient);

        try {
            $this->assertNull($hook->do());
        }
        catch (Exception $e) {
            $this->fail();
        }
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 5
     */
    public function testMockBadParser()
    {
        $hook = new Hook(null, []);

        $hook->do();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 0
     */
    public function testBadLogFile()
    {
        $hook = new Hook(null, [], "");

        $hook->do();
    }

    /**
     * @expectedException DEX\HookException
     * @expectedExceptionCode 5
     */
    public function testNullLogFile()
    {
        $hook = new Hook(null, [], null);

        $hook->do();
    }
}
