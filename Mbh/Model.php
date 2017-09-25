<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-mvc
 * @copyright Copyright (c) 2017 COD-Project (https://github.com/COD-Project)
 * @license   https://github.com/MBHFramework/mbh-mvc/blob/PHP5.6-1.x/LICENSE (MIT License)
 */

namespace Mbh;

use Mbh\Connection\StdConnection as Connection;
use Mbh\Interfaces\ModelInterface;

/**
 * created by Lucas Di Cunzolo
 */
class Model extends ModelInterface
{
    protected $db = null;
    protected $table = [];
    protected $columnData = [];

    function __construct()
    {
        $db = Connection::start();
    }

    function __destruct()
    {
        $db = $table = $columnData = null;
    }
}
