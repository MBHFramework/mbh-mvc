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
 * created by Ulises Jeremias Cornejo Fandos
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

    public function __construct($state = [], $default = null)
    {
        foreach (static::$columnData as $key => $value) {
            $this->state[$key] = $default;
        }

        $this->addState($state);
    }

    /**
     * Calling a non-existant method on Model checks to see if there's an item
     * in the state.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (!array_key_exists($method, $this->state)) {
            throw new \BadMethodCallException("Method $method is not a valid method");
        }

        return $this->getStateAttr($method, ...$args);
    }

    /**
     * Does app have a setting with given key?
     *
     * @param string $key
     * @return bool
     */
    public function hasStateAttr($key)
    {
        return isset($this->state[$key]);
    }

    /**
     * Get model state
     *
     * @return array
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get model state attr with given key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getStateAttr($key, $defaultValue = null)
    {
        return $this->hasStateAttr($key) ? $this->state[$key] : $defaultValue;
    }

    /**
     * Merge a key-value array with existing app settings
     *
     * @param array $state
     */
    public function addState($state)
    {
        $this->state = array_merge($this->state, $state);
    }

    public function exists()
    {
        if (!static::get($this->state)) {
            return false;
        }

        return true;
    }

    protected function matches()
    {
        $matches = [];

        foreach ($this->state as $key => $value) {
            if (!isset(static::$columnData[$key])) {
                throw new \RuntimeException("Key $key does not match with any column of " . static::$table['name'] . " table");
            } elseif ($value != null) {
                $matches[static::$columnData[$key]] = $value;
            }
        }

        return $matches;
    }

    public function equals($model)
    {
        $id = static::$table['idColumn'];
        $hash_key = array_search($id, static::$columnData);
        $className = get_called_class();

        if (!$model instanceof $className) {
            return false;
        }

        return $this->getStateAttr($hash_key) === $model->getStateAttr($hash_key);
    }

    /**
     * Insert a series of elements into a table in the database
     *
     * @param array $e: Associative arrangement of elements, with the 'field_en_la_tabla' => 'value_to_insertar_en_ese_campo',
     *                  all elements of the array $ e, will be healed by the method without having to do it manually when creating the array...
     *
     * @return object \PDOStatement
     */
    public static function insert($e)
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->insert(static::$table['name'], $e);
    }

    /**
     * Clears a series of items securely from a table in the database
     *
     * @param string $where: Deletion condition that defines who are those elements
     * @param string $limit: By default it is limited to deleting a single element that matches the $ where
     *
     * @return object \PDOStatement
     */
    public function delete($where, $limit = 'LIMIT 1')
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->delete(static::$table['name'], $where, $limit);
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
    public static function update($e, $where = "1=1", $limit = "")
    {
        if (!static::$db) {
            static::init();
        }

        return static::$db->update(static::$table['name'], $e, $where, $limit);
    }
    
    public static function updateWith($data)
    {
        $column = array_search(static::$table['idColumn'], static::$columnData);
        $class = get_called_class();
        foreach ($data as $key => $value) {
            $new_data = new $class($value);
            if (!$new_data->refresh()->getStateAttr($column)) {
                $new_data->save();
            }
        }
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
    public static function select($e = "*", $where = "1=1", $limit = "")
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

        /* if (!$model->isValid()) {
            return;
        } */

        return $model->save();
    }

    public static function find($id)
    {
        $column = array_search(static::$table['idColumn'], static::$columnData);
        return static::findBy($id, $column)[0];
    }

    public static function findBy($value, $column, $from = null, $delta = null)
    {
        return static::get([
          $column => $value
        ], $from, $delta);
    }

    public static function get($criteria = [], $from = null, $delta = null)
    {
        $className = get_called_class();
        $where = "1=1";

        foreach ($criteria as $key => $value) {
            if (isset(static::$columnData[$key]) and $value !== null) {
                $where .= " AND " . static::$columnData[$key] . "='$value'";
            }
        }

        if ($from) {
            $limit = $delta ? "LIMIT $from, $delta" : "LIMIT $from";
        }

        $limit = "";
        
        $result = static::select("*", $where, $limit);

        $models = [];
        
        foreach ($result as $row => $content) {
            $data = [];

            foreach ($content as $key => $value) {
                $hash_key = array_search($key, static::$columnData);
                $data[$hash_key] = $value;
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
        $hash_key = array_search($id, static::$columnData);

        $this->refresh();

        return $this;
    }

    public function edit()
    {
        $matches = $this->matches();
        $idColumn = static::$table['idColumn'];

        static::$db->update(
          static::$table['name'],
          $matches,
          "$idColumn=" . $matches[$idColumn],
          "LIMIT 1"
        );

        return $this;
    }

    public function refresh()
    {
        $users = static::get($this->state);

        if (count($users) > 0) {
            $this->state = $users[0]->state;
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
              "LIMIT 1"
            );
        }

        return $this;
    }

    public function __toString()
    {
        return json_encode($this->state);
    }
}
