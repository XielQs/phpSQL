<?php

/**
 * Coder By GamerboyTR (https://github.com/gamerboytr)
 * A Php Library For MySqli
 * Version 1.3.4
 * License Under MIT
 */

namespace GamerboyTR;

use mysqli;

/**
 * phpSQL Library
 * 
 * @category MySQLi
 * @package  gamerboytr/phpsql
 * @author   GamerboyTR <offical.gamerboytr@yandex.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  Release 1.3.4
 * @link     http://github.com/gamerboytr/phpSQL
 */
class phpSQL
{
    /**
     * Database
     *
     * @var string
     */
    protected $_database = null;
    /**
     * MySQLi Username
     *
     * @var string
     */
    protected $_databaseUser = "root";
    /**
     * MySQLi Password
     *
     * @var string
     */
    protected $_databasePassword = "";
    /**
     * MySQLi Host
     *
     * @var string
     */
    protected $_databaseHost = "localhost";

    /**
     * Set Class MySQLi Config
     * 
     * @param string $host     MySQLi Host
     * @param string $username MySQLi Username
     * @param string $password MySQLi Password
     * 
     * @return array MySQLi Status
     */
    function __construct($host = null, $username = null, $password = null)
    {
        $this->_databaseHost = $host ? $host : $this->_databaseHost;
        $this->_databaseUser = $username ? $username : $this->_databaseUser;
        $this->_databasePassword = $password ? $password : $this->_databasePassword;
        if (!$this->get_status()['success']) {
            die("<br>MySQLi Error : " . $this->get_status()['error']);
        };
    }
    /**
     * Save Your Class Configuration
     * 
     * @param array $options Save Options
     * 
     * @return array
     */
    public function save_config($options = [])
    {
        $options = array_merge(["fileSavePath" => "./", "overwriteFile" => true], $options);
        $filePath = $options['fileSavePath'] . "phpsql.config.json";
        if (file_exists($filePath) && !$options['overwriteFile']) {
            return [
                "success" => false,
                "error" => "phpSQL Config File Already Exits!"
            ];
        }
        $file = fopen($filePath, "w+") or die("Unable to open file!");
        $json = json_encode([
            "database" => [
                "current" => $this->_database,
                "user" => $this->_databaseUser,
                "password" => $this->_databasePassword,
                "host" => $this->_databaseHost
            ],
            "date" => date("Y-m-d H:i:s"),
        ]);
        if (fwrite($file, $json)) return ["success" => true];
    }
    /**
     * Restore Your Class Configuration
     * 
     * @param string $path Configuration File Path
     * 
     * @return array
     */
    public function restore_config($path)
    {
        $filePath = $path . "phpsql.config.json";
        if (!file_exists($filePath)) {
            return [
                "success" => false,
                "error" => "File Does Not Exist"
            ];
        }
        $file = file_get_contents($filePath) or die("Unable to open file!");
        $file = json_decode($file);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                "success" => false,
                "error" => "Configuration File Corrupt Or Incorrect"
            ];
        }
        try {
            $this->_databaseHost = $file->database->host;
            $this->_databaseUser = $file->database->user;
            $this->_databasePassword = $file->database->password;
            $this->_database = $file->database->current;
            if ($this->get_status()['success']) return ["success" => true];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "error" => $th
            ];
        }
    }
    /**
     * Connect MySQLi
     * 
     * @return object MySQLi Variable
     */
    public function connect()
    {
        try {
            @$mysqli = new mysqli($this->_databaseHost, $this->_databaseUser, $this->_databasePassword, $this->_database);
            if ($mysqli->connect_errno) die("<br>MySQLi Connect Error : " . $mysqli->connect_error);
            return $mysqli;
        } catch (\Throwable $th) {
            die("<br>MySQLi Connect Error : $th");
        }
    }
    /**
     * Get Class Connection Status
     * 
     * @return array Status
     */
    public function get_status()
    {
        try {
            @$mysqli = $this->connect();
            if ($mysqli->connect_errno) {
                return [
                    "success" => 0,
                    "errorCode" => $mysqli->connect_errno,
                    "error" => $mysqli->connect_error,
                ];
            } else {
                return ["success" => 1];
            }
        } catch (\Throwable $th) {
            return [
                "success" => 0,
                "error" => $th,
            ];
        }
    }
    /**
     * Set Class MySQLi Config
     * 
     * @param string $host     MySQLi Host
     * @param string $username MySQLi Username
     * @param string $password MySQLi Password
     * 
     * @return array MySQLi Status
     */
    public function set_config($host, $username, $password)
    {
        $this->_databaseHost = $host ? $host : $this->_databaseHost;
        $this->_databaseUser = $username ? $username : $this->_databaseUser;
        $this->_databasePassword = $password ? $password : $this->_databasePassword;
        if (!$this->get_status()['success']) {
            die("<br>MySQLi Error : " . $this->get_status()['error']);
        };
    }
    /**
     * Returning Class MySQLi Config (e.g. database,user)
     * 
     * @return array Config
     */
    public function get_config()
    {
        return [
            "database" => $this->_database,
            "user" => $this->_databaseUser,
            "password" => $this->_databasePassword,
            "host" => $this->_databaseHost,
            "active" => $this->get_status()['success'],
        ];
    }
    /**
     * Set Class Database
     * 
     * @param string $database Database Name
     * 
     * @return boolean
     */
    public function select_db($database)
    {
        $this->_database = $database;
        if ($this->get_status()['success']) {
            return true;
        } else {
            return $this->get_status();
        }
    }
    /**
     * MySQLi Query
     * 
     * @param string  $query MySQLi Query
     * @param boolean $fetch Fetch Query
     * 
     * @return mixed Data From Table
     */
    public function query($query, $fetch = true)
    {
        if (!$this->_database) die("<br>Please Select a Database First !");
        try {
            @$mysqli = $this->connect();
            $myQuery = $mysqli->query($query);
            if (!$myQuery) die("<br>A MySQLi Query Error Occurred : " . $mysqli->error);
            if ($fetch) return $myQuery->fetch_assoc();
            else return $myQuery;
        } catch (\Throwable $th) {
            die("<br>MySQLi Error : $th");
        }
    }
    /**
     * Create Table
     * 
     * @param string $name  Table Name
     * @param array  $props Table Props
     * 
     * @return array
     */
    public function create_table($name, $props)
    {
        if (!is_array($props)) die("<br>Props Must Be Array In Array");
        $str = "CREATE TABLE $name (";
        foreach ($props as $key => $val) {
            if (empty($val['name']) || empty($val['type'])) die("<br>Name And Type Required");
            $val = array_merge([
                "length" => 11,
                "AI" => false,
                "unique" => null,
                "isnull" => false,
                "comment" => null
            ], $val);
            if (!is_array($val)) die("<br>Props Must Be Array In Array");
            $str .= "`" . $val['name'] . "` " . $val['type'] . "(" . $val['length'] . ") " . (!$val['isnull'] ? 'NOT NULL ' : '') . ($val['AI'] ? 'AUTO_INCREMENT' : '') . ($val['comment'] ? 'COMMENT "' . $val['comment'] . '"' : '') . " " . $val['unique'] . ",";
        }
        $str = trim(mb_substr(preg_replace("/\s\s/", " ", trim($str)), 0, -1));
        $str .= ")";
        $mysqli = $this->connect();
        $mysqli->select_db($this->_database);
        $query = $mysqli->query($str);
        if ($query) return ["success" => true];
        else return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
            "tryingCode" => $str,
        ];
    }
    /**
     * Create Database
     *
     * @param string $name
     * 
     * @return array
     */
    public function create_db($name)
    {
        $mysqli = $this->connect();
        $tryingCode = "CREATE DATABASE $name";
        $query = $mysqli->query($tryingCode);
        if ($query && !$mysqli->errno) return ["success" => true];
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
        ];
    }
    /**
     * Select Row From Table
     * 
     * @param string $column Select Column (* = Select All)
     * @param string $table  Selecting Data Table
     * @param string $extra  Extra Selector (e.g. WHERE, ORDER BY)
     * @param string $fetch  Fetch Query
     * 
     * @return mixed
     */
    public function select($column, $table, $extra = null, $fetch = true)
    {
        @$mysqli = $this->connect();
        $query = $mysqli->query("SELECT $column FROM $table" . ($extra ? ' ' . $extra : ''));
        if ($mysqli->errno || !$query) die("<br>Error Fetching Column : " . $mysqli->error);
        if ($fetch) return $query->fetch_assoc();
        else return $query;
    }
    /**
     * Delete Row From Table
     * 
     * @param string $table Name Of The Table
     * @param string $where Where Condition
     * 
     * @return array
     */
    public function delete($table, $where)
    {
        $mysqli = $this->connect();
        $tryingCode = "DELETE FROM `$table` WHERE $where";
        @$query = $mysqli->query($tryingCode);
        if (!$mysqli->affected_rows) return [
            "success" => false,
            "error" => "No Data Found",
            "errorType" => "NO_AFFECTED_ROWS",
            "tryingCode" => $tryingCode,
        ];
        if ($query || !$mysqli->errno) return ["success" => true];
        else return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
            "tryingCode" => $tryingCode,
        ];
    }
    /**
     * Adding Data to an Existing Table
     *
     * @param string $table Table Name
     * @param array  $props Data
     * 
     * @return array
     */
    public function insert($table, $props)
    {
        if (!$this->_database) return [
            "success" => false,
            "error" => "Please Select Database First !",
        ];
        if (!is_array($props)) die("Props Must Be Array");
        $str = "INSERT INTO `$table` (";
        foreach ($props as $key => $val) $str .= "$key, ";
        $str = trim(mb_substr(preg_replace("/\s\s/", " ", trim($str)), 0, -1));
        $str .= ") VALUES (";
        foreach ($props as $key => $val) {
            if (gettype($val) !== "boolean") $str .= "'$val', ";
            else $str .= "$val, ";
        }
        $str = trim(mb_substr(preg_replace("/\s\s/", " ", trim($str)), 0, -1));
        $str .= ")";
        $mysqli = $this->connect();
        @$query = $mysqli->query($str);
        if ($query && !$mysqli->errno) return ['success' => true];
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
            "tryingCode" => $str
        ];
    }
    /**
     * Update Data From Existing Table
     *
     * @param string $table Table Name
     * @param array  $props Data
     * @param string $where Where Condition
     * 
     * @return array
     */
    public function update($table, $props, $where)
    {
        if (!$this->_database) return [
            "success" => false,
            "error" => "Please Select Database First !",
        ];
        if (!is_array($props)) die("Props Must Be Array");
        $str = "UPDATE $table SET ";
        foreach ($props as $key => $val) $str .= "$key='$val', ";
        $str = trim(mb_substr(preg_replace("/\s\s/", " ", trim($str)), 0, -1));
        $str .= "WHERE $where";
        $mysqli = $this->connect();
        @$query = $mysqli->query($str);
        if ($query && !$mysqli->errno) return ['success' => true];
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
            "tryingCode" => $str
        ];
    }
    /**
     * Shows All Tables In The Database
     *
     * @param string $db Database (if empty it uses the database in the class)
     * 
     * @return array|void Tables
     */
    public function get_tables($db = null)
    {
        $db = $db ? $db : $this->_database;
        $mysqli = $this->connect();
        $mysqli->select_db($db);
        $query = $mysqli->query("show TABLES");
        if (!$mysqli->errno && $query) {
            $arr = ["success" => true, "tables" => []];
            foreach ($query->fetch_all() as $key => $val) {
                array_push($arr['tables'], $val[0]);
            }
            return $arr;
        };
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error
        ];
    }
    /**
     * Shows All Databases
     * 
     * @return array|void Databases
     */
    public function get_dbs()
    {
        $mysqli = $this->connect();
        $query = $mysqli->query("show DATABASES");
        if (!$mysqli->errno && $query) {
            $arr = ["success" => true, "databases" => []];
            foreach ($query->fetch_all() as $key => $val) {
                array_push($arr['databases'], $val[0]);
            }
            return $arr;
        };
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error
        ];
    }
    /**
     * Remove Table|Database
     *
     * @param string $name     Table Or Database Name
     * @param string $droptype Table Or Database
     * 
     * @return array
     */
    public function drop($name, $droptype)
    {
        $mysqli = $this->connect();
        $tryingCode = "DROP ";
        $tryingCode .= strtolower($droptype) === "table" ? "TABLE $name" : (strtolower($droptype) === "database" ? "DATABASE $name" : null);
        if (trim($tryingCode) === "DROP") return [
            "success" => false,
            "error" => "$droptype Is Not Valid Type, droptype Must Be Table Or Database",
        ];
        @$query = $mysqli->query($tryingCode);
        if ($query && !$mysqli->errno) return ["success" => true];
        return [
            "success" => false,
            "errorCode" => $mysqli->errno,
            "error" => $mysqli->error,
        ];
    }
}
