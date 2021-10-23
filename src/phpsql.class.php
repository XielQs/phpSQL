<?php

/**
 * Coder By GamerboyTR (https://github.com/gamerboytr)
 * A Php Library For MySql
 * Version 1.3
 * License Under MIT
 */

namespace GamerboyTR;

use mysqli;

/**
 * phpSQL Library
 * 
 * @category MySQL
 * @package  gamerboytr/phpsql
 * @author   GamerboyTR <offical.gamerboytr@yandex.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  Release 1.3
 * @link     http://github.com/gamerboytr/phpSQL
 */
class phpSQL
{
    protected $database = null;
    protected $databaseUser = "root";
    protected $databasePassword = "";
    protected $databaseHost = "localhost";

    function __construct($host = null, $username = null, $password = null)
    {
        $this->databaseHost = $host ? $host : $this->databaseHost;
        $this->databaseUser = $username ? $username : $this->databaseUser;
        $this->databasePassword = $password ? $password : $this->databasePassword;
        if (!$this->getStatus()['success']) {
            die("<br>Mysqli Error : " . $this->getStatus()['error']);
        };
    }
    /**
     * Save Your Mysqli Configuration
     * 
     * @param array $options Save Options
     * 
     * @return array
     */
    public function saveMysqliConfig($options = [])
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
                "current" => $this->database,
                "user" => $this->databaseUser,
                "password" => $this->databasePassword,
                "host" => $this->databaseHost
            ],
            "date" => date("Y-m-d H:i:s"),
        ]);
        if (fwrite($file, $json)) return ["success" => true];
    }
    /**
     * Restore Your Mysqli Configuration
     * 
     * @param string $path Configuration File Path
     * 
     * @return array
     */
    public function restoreMysqliConfig($path)
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
            $this->databaseHost = $file->database->host;
            $this->databaseUser = $file->database->user;
            $this->databasePassword = $file->database->password;
            $this->database = $file->database->current;
            if ($this->getStatus()['success']) return ["success" => true];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "error" => $th
            ];
        }
    }
    /**
     * Connect Mysqli
     * 
     * @return object Mysql Variable
     */
    public function connect()
    {
        try {
            @$mysqli = new mysqli($this->databaseHost, $this->databaseUser, $this->databasePassword, $this->database);
            if ($mysqli->connect_errno) die("<br>Mysqli Connect Error : " . $mysqli->connect_error);
            return $mysqli;
        } catch (\Throwable $th) {
            die("<br>Mysqli Connect Error : $th");
        }
    }
    /**
     * Get Mysqli Status
     * 
     * @return array Status
     */
    public function getStatus()
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
     * Set Mysqli Config
     * 
     * @param string $host     Mysqli Host
     * @param string $username Mysqli Username
     * @param string $password Mysqli Password
     * 
     * @return array Mysqli Status
     */
    public function setMysqli($host, $username, $password)
    {
        $this->databaseHost = $host ? $host : $this->databaseHost;
        $this->databaseUser = $username ? $username : $this->databaseUser;
        $this->databasePassword = $password ? $password : $this->databasePassword;
        if (!$this->getStatus()['success']) {
            die("<br>Mysqli Error : " . $this->getStatus()['error']);
        };
    }
    /**
     * Returning Mysqli Details
     * 
     * @return array Details
     */
    public function getMysqliDetails()
    {
        return [
            "database" => $this->database,
            "user" => $this->databaseUser,
            "password" => $this->databasePassword,
            "host" => $this->databaseHost,
            "active" => $this->getStatus()['success'],
        ];
    }
    /**
     * Set Mysqli Database
     * 
     * @param string $database Database Name
     * 
     * @return boolean
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        if ($this->getStatus()['success']) {
            return true;
        } else {
            return $this->getStatus();
        }
    }
    /**
     * Return Mysqli Query
     * 
     * @param string  $query Mysqli Query
     * @param boolean $fetch Fetch Query
     * 
     * @return mixed Data From Table
     */
    public function query($query, $fetch = true)
    {
        if (!$this->database) die("<br>Please Select a Database First !");
        try {
            @$mysqli = $this->connect();
            $myQuery = $mysqli->query($query);
            if (!$myQuery) die("<br>A Mysqli Query Error Occurred : " . $mysqli->error);
            if ($fetch) return $myQuery->fetch_assoc();
            else return $myQuery;
        } catch (\Throwable $th) {
            die("<br>Mysqli Error : $th");
        }
    }
    /**
     * Create Table For Database
     * 
     * @param string $name  Table Name
     * @param array  $props Table Props
     * 
     * @return array
     */
    public function createTable($name, $props)
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
        $mysqli->select_db($this->database);
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
    public function createDatabase($name)
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
        if (!$this->database) return [
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
        if (!$this->database) return [
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
    public function getTables($db = null)
    {
        $db = $db ? $db : $this->database;
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
    public function getDatabases()
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
}
