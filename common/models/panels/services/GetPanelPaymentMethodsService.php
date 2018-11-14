<?php
namespace common\models\panels\services;

use common\models\panels\PanelPaymentMethods;
use common\models\panels\Project;
use yii\db\Query;

/**
 * Class GetPanelPaymentMethodsService
 * @package common\models\panels\services
 */
class GetPanelPaymentMethodsService {

    /**
     * @var Project
     */
    private $_panel;

    /**
     * @var integer|null
     */
    private $_visibility;

    /**
     * GetPanelPaymentMethodsService constructor.
     * @param Project $panel
     * @param integer|null $visibility
     */
    public function __construct(Project $panel, ?int $visibility = null)
    {
        $this->_panel = $panel;
        $this->_visibility = $visibility;
    }

    /**
     * @return array
     */
    public function get()
    {
        $query = (new Query())
            ->select([
                'id',
                'method_id',
                'currency_id',
                'name',
                'minimal',
                'maximal',
                'options',
                'visibility',
                'new_users',
                'take_fee_from_user',
            ])
            ->from(['ppm' => DB_PANELS . '.' . PanelPaymentMethods::tableName()])
            ->andWhere([
                'ppm.panel_id' => $this->_panel->id
            ])
            ->orderBy([
                'position' => SORT_ASC
            ]);

        $paymentMethods = [];

        if (null !== $this->_visibility) {
            $query->andWhere([
                'visibility' => (int)$this->_visibility
            ]);
        }

        foreach ($query->all() as $method) {
            $paymentMethods[$method['method_id']] = [
                'id' => $method['id'],
                'method_id' => $method['method_id'],
                'currency_id' => $method['currency_id'],
                'name' => $method['name'],
                'minimal' => $method['minimal'],
                'maximal' => $method['maximal'],
                'options' => !empty($method['options']) ? json_decode($method['options'], true) : [],
                'visibility' => (int)$method['visibility'],
                'new_users' => (int)$method['new_users'],
                'take_fee_from_user' => (int)$method['take_fee_from_user'],
            ];
        }

        return $paymentMethods;
    }
}