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
    protected $db = null;

    protected $table = [];

    protected $columnData = [];

    public function __construct($settings)
    {
        $db = Connection::create($settings);
    }

    /**
     * Insert a series of elements into a table in the database
     *
     * @param array $e: Associative arrangement of elements, with the 'field_en_la_tabla' => 'value_to_insertar_en_ese_campo',
     *                  all elements of the array $ e, will be healed by the method without having to do it manually when creating the array...
     *
     * @return object \PDOStatement
     */
    public function insert($e)
    {
        return $this->db->insert($table['name'], $e);
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
    public function update($e, $where = "1 = 1", $limit = "")
    {
        return $this->db->update($table['name'], $e, $where, $limit);
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
    public function select($e, $where = "1 = 1", $limit = "")
    {
        return $this->db->select($e, $table['name'], $where, $limit);
    }

    public function __destruct()
    {
        $db = $table = $columnData = null;
    }
}
