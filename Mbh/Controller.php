<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-mvc
 * @copyright Copyright (c) 2017 COD-Project (https://github.com/COD-Project)
 * @license   https://github.com/MBHFramework/mbh-mvc/blob/PHP5.6-1.x/LICENSE (MIT License)
 */

namespace Mbh;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Mbh\Helpers\Inflect;
use Mbh\Interfaces\ControllerInterface;

/**
 * created by Lucas Di Cunzolo
 */
class Controller
{
    protected static $model;

    protected $template;

    protected $app;

    function __construct($app = null)
    {
        $className = explode('\\', get_called_class());

        $modelName = ucwords(Inflect::pluralize(
                       str_replace("Controller", "", $className[count($className)-1])
                     ));

        $this->model = !class_exists($modelName) ?: $modelName;
        $this->app = $app;
        $this->template = new Twig_Environment(new Twig_Loader_Filesystem('./web/templates/')); 
    }
}
