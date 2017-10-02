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
class Model implements ModelInterface
{
    protected static $db = null;

    protected static $table = [];

    protected static $columnData = [];

    protected $state = [];

    public static function init($settings = [], $new_instance = true)
    {
          if (!static::$db instanceof Connection or $new_instance) {
              static::$db = Connection::create($settings);
          }

          return static::$db;
    }

    public function __construct($state = [])
    {
        $this->state = $state;
    }

    /**
     * Calling a non-existant method on App checks to see if there's an item
     * in the container that is callable and if so, calls it.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (!isset($this->state[$method])) {
            throw new \BadMethodCallException("Method $method is not a valid method");
        }

        return $this->state[$method];
    }

    public function exists()
    {
        return count(static::get($this->state)) > 0;
    }

    protected function matches()
    {
        $matches = [];

        foreach ($this->state as $key => $value) {
            if (!isset(static::$columnData[$key])) {
                throw new \RuntimeException("Key $key does not match with any column of " . static::$table['name'] . " table");
            }
            $matches[static::$columnData[$key]] = $value;
        }

        return $matches;
    }

    /**
     * Insert a series of elements into a table in the database
     *
     * @param array $e: Associative arrangement of elements, with the 'field_en_la_tabla' => 'value_to_insertar_en_ese_campo',
     *                  all elements of the array $ e, will be healed by the method without having to do it manually when creating the array...
     *
     * @return object \PDOStatement
     */
    protected static function insert($e)
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->insert(static::$table['name'], $e);
    }

    /**
     * Updates elements of a table in the database according to a condition
     *
     * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
     *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
     * @param string $where: Condition indicating who will be modified
     * @param string $limite: Limit modified elements, by default modifies them all
     *
     * @return object \PDOStatement
     */
    protected static function update($e, $where = "1 = 1", $limit = "")
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->update(static::$table['name'], $e, $where, $limit);
    }

    /**
     * Selects and lists in an associative / numeric array the results of a search in the database
     *
     * @param string $e: Elements to Select Comma Separated
     * @param string $where: Condition that indicates who are the ones that are extracted, if not placed extracts all
     * @param string $limite: Limit of items to bring, by default brings ALL those that match $where
     *
     * @return False if you do not find any results, array associative / numeric if you get at least one
     */
    protected static function select($e = "*", $where = "1 = 1", $limit = "")
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->select($e, static::$table['name'], $where, $limit);
    }

    public static function create($data = [])
    {
        $className = get_called_class();
        $model = new $className($data);

        if (!$model->isValid()) {
            return;
        }

        return $model->save();
    }

    public static function find($id)
    {
        $column = static::$columnData[static::$table['idColumn']];
        return static::findBy($id, $column);
    }

    public static function findBy($value, $column)
    {
        return static::get([
          $column => $value
        ]);
    }

    public static function get($criteria = [])
    {
        $className = get_called_class();
        $models = [];

        $where = "1=1";
        foreach ($criteria as $key => $value) {
            if (isset($columnData[$key])) {
                $where .= " AND " . $columnData[$key] . "=$value";
            }
        }

        $result = static::select("*", $where);

        foreach ($result as $row => $content) {
            $data = [];

            foreach ($content as $key => $value) {
                $data[$columnData[$key]] = $value;
            }

            $models[] = new $className($data);
        }

        return $models;
    }

    public static function all()
    {
        return static::get();
    }

    public function save()
    {
        static::insert($this->matches());
        $id = static::$table['idColumn'];
        $this->state[$id] = static::$db->lastInsertId();

        return $this;
    }

    public function edit()
    {
        if ($this->exists()) {
            $matches = $this->matches();
            $idColumn = static::$table['idColumn'];

            static::update(
              $matches,
              "$idColumn=" . $matches[$idColumn],
              1
            );
        } else {
            $this->save();
        }

        return $this;
    }

    public function remove()
    {
        if ($this->exists()) {
            $matches = $this->matches();
            $idColumn = static::$table['idColumn'];

            static::delete(
              "$idColumn=" . $matches[$idColumn],
              1
            );
        }

        return $this;
    }

    public function __destruct()
    {
        $db = $table = $columnData = null;
    }
}
