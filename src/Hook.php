<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 15.11.2017
 * Time: 13:29
 */

namespace DEX;

use Zend\Http\Client;
use Zend\Config\Config;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Http\Client\Exception\RuntimeException;
use Zend\Math\Rand;

class Hook
{
    /**
     * @var string
     */
    private $mark = "";

    /**
     * @var Config
     */
    private $config = null;

    /**
     * @var Client
     */
    private $client = null;

    /**
     * @var IParser
     */
    private $parser = null;

    /**
     * @var Logger
     */
    private $logger = null;

    /**
     * Hook constructor.
     * @param $parser IParser
     * @param $config array
     * @param $logger Logger|string|null
     * @throws \Exception Log file
     */
    public function __construct($parser, $config, $logger = './tests.log')
    {
        $this->mark = Rand::getString(4, 'abcdefghijklmnopqrstuvwxyz');

        $this->logger = $logger;

        if (gettype($logger) == "string") {
            $this->logger = new Logger;

            $stream = @fopen($logger, 'a', false);
            if (!$stream) {
                throw new \Exception('Failed to open stream');
            }

            $writer = new Stream($stream);
            $this->logger->addWriter($writer);
        }
        else {
            if ($this->logger === null) {
                $this->logger = new Logger;
                $writer = new Stream('php://output');

                $this->logger->addWriter($writer);
            }
        }

        $this->parser = $parser;
        $this->config = new Config($config);
        $this->client = new Client($this->config->url);
        $this->client->setAdapter(new Client\Adapter\Curl());
        $this->client->setOptions(['timeout' => 15]);
    }

    /**
     * @throws RuntimeException Http error.
     * @throws HookException
     * @return null
     */
    public function do()
    {
        if (!$this->parser) {
            throw new HookException("Bad parser", 5);
        }

        $this->logger->info($this->mark . " do: Start");

        $this->logger->info($this->mark . " do: CURL: " . $this->config->url);
        $response = $this->client->send();

        $body = $response->getBody();
        $this->logger->info($this->mark . " do: Body size: " . strlen($body));

        $this->logger->info($this->mark . " do: Validation");
        if (!$this->parser->validation($body)) {
            throw new HookException("Bad validation", 1);
        }

        $this->logger->info($this->mark . " do: Before");
        if (!$this->parser->before($this->config)) {
            throw new HookException("Bad before init", 2);
        }

        $this->logger->info($this->mark . " do: Run");
        if (!$this->parser->run($body)) {
            throw new HookException("Bad run", 3);
        }

        $this->logger->info($this->mark . " do: After");
        if (!$this->parser->after()) {
            throw new HookException("Bad after", 4);
        }

        $this->logger->info($this->mark . " do: End successfully");

        return null;
    }
}