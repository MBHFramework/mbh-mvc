<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 COD-Project
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */

namespace Mbh\Connection\Engines;

use Mbh\Connection\StdConnection;

/**
 * created by Ulises Jeremias Cornejo Fandos
 */
class Mysql extends StdConnection
{
    public function __construct($database = [])
    {
        parent::__construct('mysql:host=' . $database['host'] . ';port=' . $database['port'] . ';dbname=' . $database['name'], $database['user'], $database['pass'], [
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
          ]);
    }
}
