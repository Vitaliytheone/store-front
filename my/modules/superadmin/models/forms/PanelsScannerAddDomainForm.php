<?php
namespace superadmin\models\forms;

use my\components\scanners\components\BasePanelInfo;
use my\components\scanners\components\info\LevopanelInfo;
use my\components\scanners\components\info\PanelfireInfo;
use common\models\panels\SuperToolsScanner;
use my\components\scanners\components\info\RentapanelInfo;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class PanelsScannerAddDomainForm
 *
 * @property string $domain
 *
 * @package superadmin\models\forms
 */
class PanelsScannerAddDomainForm extends Model
{
    /** @var  string */
    public $domain;

    /** @var  string */
    private $_panelInfo;

    private static $_allowedStatuses = [
        SuperToolsScanner::PANEL_STATUS_ACTIVE,
        SuperToolsScanner::PANEL_STATUS_DISABLED,
        SuperToolsScanner::PANEL_STATUS_PERFECTPANEL,
    ];

    public function formName()
    {
        return 'AddDomainForm';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['domain', 'required'],
            ['domain', 'trim'],
            ['domain', 'url', 'defaultScheme' => 'http', 'message' => Yii::t('app/superadmin', 'tools.levopanel.error.invalid_domain_name')],
            ['domain', 'domainFilter'],
        ];
    }

    /**
     * Set current panel info model by scanner name
     * @param $panelType integer
     * @return string
     */
    public function setPanelInfo($panelType)
    {
        switch ($panelType) {
            case SuperToolsScanner::PANEL_LEVOPANEL :
                $this->_panelInfo = LevopanelInfo::class;
                break;
            case SuperToolsScanner::PANEL_PANELFIRE :
                $this->_panelInfo = PanelfireInfo::class;
                break;
            case SuperToolsScanner::PANEL_RENTALPANEL :
                $this->_panelInfo = RentapanelInfo::class;
                break;
            default :
                exit(Yii::t('app/superadmin', 'tools.panels_scanner.error.unresolved_type') . $panelType);
                break;
        }
    }

    /**
     * Get current panel info model class name
     * @return string
     */
    public function getPanelInfo()
    {
        return $this->_panelInfo;
    }

    /**
     * Check status and add panel domain
     * @param $panelType
     * @param $postData
     * @return bool
     */
    public function addDomain($panelType, $postData)
    {
        if (!in_array($panelType, SuperToolsScanner::$panels)) {
            return false;
        }

        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        $panelData = $this->getDomainData();
        $panelStatus = $panelData['status'];
        $panelInfo = $panelData['info'];

        $exitingDomainModel = SuperToolsScanner::find()
            ->andWhere([
                'domain' => $this->domain,
                'panel' => $panelType,
            ])
            ->one();

        if ($exitingDomainModel) {

            $this->addError('domain', Yii::t('app/superadmin', 'tools.levopanel.error.domain_exist', ['domain' => $this->domain]));

            return false;
        }

        if (!$exitingDomainModel && in_array($panelStatus, static::$_allowedStatuses)) {
            $domainModel = new SuperToolsScanner();
            $domainModel->setAttributes([
                'domain' => $this->domain,
                'panel_id' => SuperToolsScanner::getNextPanelId($panelType),
                'panel' => $panelType,
                'server_ip' => ArrayHelper::getValue($panelInfo, 'primary_ip', null),
                'status' => $panelStatus,
                'details' => json_encode($panelInfo),
            ]);

            return $domainModel->save();
        }

        $this->addError('domain', Yii::t('app/superadmin', 'tools.levopanel.error.status_not_allowed', ['status' => SuperToolsScanner::getStatusName($panelStatus)]));

        return false;
    }

    /**
     * Domain filter
     * @param $attribute
     * @return bool
     */
    public function domainFilter($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $value = mb_strtolower($this->{$attribute});
        $value = trim($value);
        $value = preg_replace("/^(http(s)?)\:\/\//uis", "", $value);
        $value = preg_replace("/^(www\.)/uis", "", $value);
        $value = parse_url('http://' . $value, PHP_URL_HOST);

        if (empty($value)) {
            $this->addError($attribute, Yii::t('app/superadmin', 'tools.panels_scanner.error.incorrect_value', ['attribute' => $attribute]));
            return false;
        }

        $this->{$attribute} = $value;

        return true;
    }

    /**
     * Domain status checker
     * @return array
     */
    public function getDomainData()
    {
        $panelInfoClassName = $this->getPanelInfo();

        if (empty($panelInfoClassName)) {
            exit(Yii::t('app/superadmin', 'tools.panels_scanner.error.bad_model_name'));
        }

        /** @var BasePanelInfo $panelInfo */
        $panelInfo = new $panelInfoClassName([
            'proxy' => ArrayHelper::getValue(Yii::$app->params, 'levopanel_scanner.proxy', []),
            'timeouts' => ArrayHelper::getValue(Yii::$app->params, 'levopanel_scanner.timeouts', []),
        ]);

        return $panelInfo->getPanelInfo($this->domain);
    }
}