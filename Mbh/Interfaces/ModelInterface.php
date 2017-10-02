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
    public static function create($data = []);

    public static function find($id);

    public static function findBy($value, $column);

    public static function get($criteria = []);

    public static function all();

    public function save();

    public function edit();

    public function remove();
}
