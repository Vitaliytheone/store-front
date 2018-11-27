<?php
namespace common\models\panels\services;

use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethods;
use common\models\panels\Project;
use Faker\Provider\kk_KZ\Payment;
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
     * @var boolean
     */
    private $_originalName = false;

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
     * @return $this
     */
    public function withOriginalName()
    {
        $this->_originalName = true;
        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        $fields = [
            'ppm.id',
            'ppm.method_id',
            'ppm.currency_id',
            'ppm.minimal',
            'ppm.maximal',
            'ppm.options',
            'ppm.visibility',
            'ppm.new_users',
            'ppm.take_fee_from_user'
        ];
        array_push($fields, $this->_originalName ? 'pm.method_name as name' : 'name');
        $query = (new Query())
            ->select($fields)
            ->from(['ppm' => DB_PANELS . '.' . PanelPaymentMethods::tableName()]);
            if ($this->_originalName) {
                $query->innerJoin(['pm' => PaymentMethods::tableName()], 'ppm.method_id = pm.id');
            }

        $query->andWhere([
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
            $paymentMethods[$method['id']] = [
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