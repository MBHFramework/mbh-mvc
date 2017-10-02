<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-mvc
 * @copyright Copyright (c) 2017 COD-Project (https://github.com/COD-Project)
 * @license   https://github.com/MBHFramework/mbh-mvc/blob/PHP5.6-1.x/LICENSE (MIT License)
 */

namespace Mbh;

use Mbh\Interfaces\ControllerInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Mbh\Helpers\Inflect;

/**
 * created by Lucas Di Cunzolo
 */
class Controller extends ControllerInterface
{
    protected $model;

    protected $template;

    protected $app;

    function __construct($app = null)
    {
      $this->app = $app;
      $this->template = new Twig_Environment(new Twig_Loader_Filesystem('./web/templates/')); 
    }
}
