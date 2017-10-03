<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-mvc
 * @copyright Copyright (c) 2017 COD-Project (https://github.com/COD-Project)
 * @license   https://github.com/MBHFramework/mbh-mvc/blob/PHP5.6-1.x/LICENSE (MIT License)
 */

namespace Mbh;

use Mbh\Helpers\Inflect;
use Mbh\Interfaces\ControllerInterface;

/**
 * created by Lucas Di Cunzolo
 */
class Controller
{
    protected $model;

    protected $template;

    protected $app;

    public static function create($controller, $args)
    {
        if(!class_exists($controller)) {
            throw new \RuntimeException;
        }

        return new $controller(...$args);
    }

    function __construct($app = null)
    {
        $className = explode('\\', get_called_class());

        $modelName = ucwords(Inflect::pluralize(
                       str_replace("Controller", "", $className[count($className)-1])
                     ));

        $this->model = !class_exists($modelName) ?: $modelName;

        $this->app = $app;
    }
}
