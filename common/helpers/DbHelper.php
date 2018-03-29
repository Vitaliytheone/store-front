<?php
namespace common\helpers;

use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Class DbHelper
 * @package common\helpers
 */
class DbHelper
{
    /**
     * @var Connection
     */
    static $_connection;

    /**
     * @var Connection[]
     */
    static $_dbConnections = [];

    /**
     * Return DB dsn attribute values
     * Useful for get current db name
     * @param $name
     * @param Connection $db
     * @return null
     */
    public static function getDsnAttribute($name, Connection $db)
    {
        if ($db && preg_match('/' . $name . '=([^;]*)/', $db->dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    /**
     * Check on exist database by name
     * @param string $name
     * @return int
     */
    public static function existDatabase($name)
    {
        $result = static::getConnection()
            ->createCommand("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$name}'")
            ->queryScalar();

        static::getConnection()->close();

        return $result;
    }

    /**
     * Create database by name
     * @param string $name
     * @return mixed
     */
    public static function createDatabase($name)
    {
        $result = static::getConnection()
            ->createCommand("CREATE DATABASE IF NOT EXISTS `{$name}`; CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
            ->execute();

        static::getConnection()->close();

        return $result;
    }

    /**
     * Drop database by name
     * @param string $name
     * @return mixed
     */
    public static function dropDatabase($name)
    {
        $result = static::getConnection()
            ->createCommand("DROP DATABASE `{$name}`;")

            ->execute();

        static::getConnection()->close();

        return $result;
    }

    /**
     * Get connection
     * @return Connection
     */
    public static function getConnection()
    {

        if (null == static::$_connection) {
            $dbConfig = static::getDbOptions();

            static::$_connection = new Connection([
                'dsn' => 'mysql:host=' . $dbConfig['host'] . ';',
                'username' => $dbConfig['user'],
                'password' => $dbConfig['password'],
            ]);
            static::$_connection->open();
        }

        return static::$_connection;
    }

    /**
     * Get connection
     * @param string $dbName
     * @return Connection
     */
    public static function getDbConnection($dbName)
    {
        if (empty(static::$_dbConnections[$dbName])) {
            $dbConfig = static::getDbOptions();

            static::$_dbConnections[$dbName] = new Connection([
                'dsn' => 'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbName,
                'username' => $dbConfig['user'],
                'password' => $dbConfig['password'],
            ]);
            static::$_dbConnections[$dbName]->open();
        }

        return ArrayHelper::getValue(static::$_dbConnections, $dbName);
    }

    /**
     * Add dump sql
     * @param string $db
     * @param string $path
     * @return bool
     */
    public static function dumpSql($db, $path)
    {
        $connection = static::getConnection();

        if (!$connection) {
            return false;
        }

        if (!static::existDatabase($db)) {
            return false;
        }

        $dbConfig = static::getDbOptions();

        $host = $dbConfig['host'];
        $username = $dbConfig['user'];
        $password = $dbConfig['password'];

        $result = shell_exec("mysql -h{$host} -u{$username} -p{$password} {$db}  < {$path}");

        $connection->close();

        return $result;
    }

    /**
     * Rename database by name
     * @param string $oldName
     * @param string $name
     * @return mixed
     */
    public static function renameDatabase($oldName, $newName)
    {
        $oldDbConnection = static::getDbConnection($oldName);
        $tables = $oldDbConnection->schema->getTableNames();
        $oldDbConnection->close();

        $sql = "CREATE DATABASE `{$newName}` COLLATE 'utf8_general_ci';";

        foreach ($tables as $table) {
            $sql .= "RENAME TABLE `{$oldName}`.`{$table}` TO `{$newName}`.`{$table}`;";
        }

        $sql .= "DROP DATABASE `{$oldName}`;";

        $result = static::getConnection()
            ->createCommand($sql)
            ->execute();

        static::getConnection()->close();

        return $result;
    }

    /**
     * @return array
     */
    protected static function getDbOptions()
    {
        $returnData = [
            'host' => null,
            'user' => null,
            'password' => null,
        ];

        if (!empty(DB_CONFIG[0]) && is_array(DB_CONFIG[0])) {
            $returnData = DB_CONFIG[0];
        } else {
            Yii::error(var_export(DB_CONFIG, true));
        }

        return $returnData;
    }
}