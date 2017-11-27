<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 15.11.2017
 * Time: 13:43
 */

namespace DEX;

use Zend\Config\Config;

interface IParser
{
    /**
     * @param $body string
     * @return boolean
     */
    public function validation($body);

    /**
     * @param $config Config
     * @return boolean
     */
    public function before($config);

    /**
     * @param $body string
     * @return boolean
     */
    public function run($body);

    /**
     * @return boolean
     */
    public function after();
}