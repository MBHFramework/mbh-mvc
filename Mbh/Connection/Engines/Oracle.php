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
class Oracle extends StdConnection
{
    public function __construct(array $database = [])
    {
        parent::__construct(
            'oci:dbname=(DESCRIPTION =
              (ADDRESS_LIST =
                (ADDRESS = (PROTOCOL = ' . $database['protocol'] . ')(HOST = ' . $database['host'] . ')(PORT = ' . $database['port'] . '))
              )
              (CONNECT_DATA =
                (SERVICE_NAME = ' . $database['name'] . ')
              )
            );charset=utf8',
                ['user'],
                ['pass'],
            [
              \PDO::ATTR_EMULATE_PREPARES => false,
              \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
              \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
}
