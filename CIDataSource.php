<?php

namespace koolreport\codeigniter;

use \koolreport\core\DataSource;
use \koolreport\core\Utility;

class CIDataSource extends DataSource
{
    static $databases;
    protected $db;
    protected $query;
    protected $sqlParams;
    protected function onInit()
    {
        $name = Utility::get($this->params, "name");
        if (CIDataSource::$databases == null) {
            CIDataSource::$databases = array();
        }

        if (isset(CIDataSource::$databases[$name])) {
            $this->db = CIDataSource::$databases[$name];
        } else {
            require_once BASEPATH . 'database/DB.php';
            $this->db = DB($name, null);
            CIDataSource::$databases[$name] = $this->db;
        }
    }

    protected function guessTypeFromValue($value)
    {
        $map = array(
            "float" => "number",
            "double" => "number",
            "int" => "number",
            "integer" => "number",
            "bool" => "number",
            "numeric" => "number",
            "string" => "string",
        );

        $type = strtolower(gettype($value));
        foreach ($map as $key => $value) {
            if (strpos($type, $key) !== false) {
                return $value;
            }
        }
        return "unknown";
    }

    public function query($query, $sqlParams = null)
    {
        $this->query = (string) $query;
        if ($sqlParams != null) {
            $this->sqlParams = $sqlParams;
        }
        return $this;
    }

    public function params($sqlParams)
    {
        $this->sqlParams = $sqlParams;
        return $this;
    }

    protected function bindParams($query, $sqlParams)
    {
        if (empty($sqlParams)) {
            return $query;
        }

        foreach ($sqlParams as $key => $value) {

            if (gettype($value) === "array") {
                $tmp = array();
                foreach ($value as $item) {
                    array_push($tmp, $this->db->escape($item));
                }
                $query = str_replace($key, "(" . implode(",", $tmp) . ")", $query);
            } else {
                $query = str_replace($key, $this->db->escape($value), $query);
            }
        }
        return $query;
    }

    public function start()
    {
        $queryString = $this->bindParams($this->query, $this->sqlParams);
        $query = $this->db->query($queryString);

        $first_row = $query->unbuffered_row('array');
        if ($first_row != null) {
            $metaData = array("columns" => array());
            foreach ($first_row as $cName => $cValue) {
                $metaData["columns"][$cName] = array(
                    "type" => $this->guessTypeFromValue($cValue),
                );
            }
            $this->sendMeta($metaData, $this);
            $this->startInput(null);
            $this->next($first_row, $this);
            while ($row = $query->unbuffered_row('array')) {
                $this->next($row, $this);
            }
            $this->endInput(null);
        } else {
            //No data
            $this->sendMeta(array("columns" => array()), $this);
            $this->startInput(null);
            $this->endInput(null);
        }

    }
}
