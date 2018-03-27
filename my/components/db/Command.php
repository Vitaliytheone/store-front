<?php
namespace my\components\db;

use Yii;
use yii\db\Exception;

/**
 * Class Command
 * @package my\components\db
 */
class Command extends \yii\db\Command {

    /**
     * Performs the actual DB query of a SQL statement.
     * @param string $method method of PDOStatement to be called
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](http://www.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return mixed the method execution result
     * @throws Exception if the query causes any problem
     * @since 2.0.1 this method is protected (was private before).
     */
    public function queryInternal($method, $fetchMode = null)
    {

        error_log('queryInternal');

        try {
            return parent::queryInternal($method, $fetchMode); // TODO: Change the autogenerated stub
        } catch (Exception $e) {

            error_log('queryInternal Exception');

            if (false !== strpos($e->getMessage(), 'server has gone away')) {
                Yii::$app->db->close();
                Yii::$app->db->open();

                return parent::queryInternal($method, $fetchMode); // TODO: Change the autogenerated stub
            }
        }
    }

    /**
     * Performs the actual DB query of a SQL statement.
     * @param string $method method of PDOStatement to be called
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](http://www.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return mixed the method execution result
     * @throws Exception if the query causes any problem
     * @since 2.0.1 this method is protected (was private before).
     */
    public function internalExecute($rawSql)
    {

        error_log('internalExecute');

        try {
            return parent::internalExecute($rawSql); // TODO: Change the autogenerated stub
        } catch (Exception $e) {

            error_log('internalExecute Exception');

            if (false !== strpos($e->getMessage(), 'server has gone away')) {
                Yii::$app->db->close();
                Yii::$app->db->open();

                return parent::internalExecute($rawSql); // TODO: Change the autogenerated stub
            }
        }
    }
}