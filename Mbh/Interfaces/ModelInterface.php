<?php

/**
 * MBHFramework
 *
 * @link      https://github.com/MBHFramework/mbh-mvc
 * @copyright Copyright (c) 2017 COD-Project (https://github.com/COD-Project)
 * @license   https://github.com/MBHFramework/mbh-mvc/blob/PHP5.6-1.x/LICENSE (MIT License)
 */

namespace Mbh\Interfaces;

/**
 * created by Lucas Di Cunzolo
 */
interface ModelInterface
{
    public function new();

    public function create();

    public function save();

    public function update();

    public function delete();

    public function get();
}
