<?php

namespace console\components\terminate;


use common\models\common\ProjectInterface;
use common\models\gateways\Sites;
use common\models\panels\Logs;
use Yii;
use yii\db\Exception as DbException;

/**
 * Class TerminateGateway
 * @package console\components\terminate
 */
class TerminateGateway
{
    /**
     * @var integer
     */
    protected $_date;

    /**
     * CancelOrder constructor.
     * @param integer $date
     */
    public function __construct($date)
    {
        $this->_date = $date;
    }

    /**
     * @throws DbException
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $site = $this->getGateway($this->_date);

        if (!$site) {
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $site->status = Sites::STATUS_TERMINATED;
            if ($site->save(false)) {
                $site->terminate();
            }
        } catch (DbException $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString());
            return;
        }
        $transaction->commit();
    }

    /**
     * @param $date
     * @return array|Sites|null
     */
    private function getGateway($date)
    {
        return Sites::find()
            ->leftJoin(DB_PANELS . '.logs', '
            logs.panel_id = sites.id AND
            logs.project_type = :project_type AND
            logs.type = :type AND logs.created_at > :date', [
                ':project_type' => ProjectInterface::PROJECT_TYPE_GATEWAY,
                ':date' => $date,
                ':type' => Logs::TYPE_RESTORED,
            ])
            ->andWhere(['sites.status' => Sites::STATUS_FROZEN])
            ->andWhere(['<', 'sites.expired', $date])
            ->andWhere('logs.id IS NULL')
            ->one();
    }
}