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
 * @author Ulises Jeremias Cornejo Fandos
 */
class Odbc extends StdConnection
{
    public function __construct(array $database = [])
    {
        parent::__construct('odbc:' . $database['name'], $database['user'], $database['pass']);
    }
}
