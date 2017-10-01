<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-framework
 * @copyright Copyright (c) 2017 Ulises Jeremias Cornejo Fandos
 * @license   https://github.com/MBHFramework/mbh-framework/blob/master/LICENSE (MIT License)
 */
namespace Mbh\Connection\Engines;

use Mbh\Connection\StdConnection;

/**
 * created by Ulises Jeremias Cornejo Fandos
 */
class Cubrid extends StdConnection
{
    public function __construct(array $database = DATABASE)
    {
        parent::__construct('cubrid:host=' . $database['host'] . ';port=' . $database['port'] . ';dbname=' . $database['name'], $database['user'], $database['pass'], [
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
          ]);
    }
}
