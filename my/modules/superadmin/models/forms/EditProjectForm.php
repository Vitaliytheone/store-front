<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\Customers;
use common\models\panels\PaymentGateway;
use common\models\panels\Tariff;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class EditProjectForm
 * @package my\modules\superadmin\models\forms
 */
class EditProjectForm extends Model {

    public $site;
    public $subdomain;
    public $name;
    public $plan;
    public $skype;
    public $cid;
    public $auto_order;
    public $lang;
    public $theme;
    public $currency;
    public $utc;
    public $package;
    public $seo;
    public $comments;
    public $mentions_wo_hashtag;
    public $mentions;
    public $mentions_custom;
    public $mentions_hashtag;
    public $mentions_follower;
    public $mentions_likes;
    public $writing;
    public $drip_feed;
    public $captcha;
    public $custom;
    public $start_count;
    public $apikey;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @var Customers[]
     */
    private $_customers;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'subdomain',
                'name',
                'plan',
                'skype',
                'cid',
                'auto_order',
                'lang',
                'theme',
                'currency',
                'utc',
                'package',
                'seo',
                'comments',
                'mentions_wo_hashtag',
                'mentions',
                'mentions_custom',
                'mentions_hashtag',
                'mentions_follower',
                'mentions_likes',
                'writing',
                'drip_feed',
                'captcha',
                'custom',
                'start_count',
                'apikey'
            ], 'safe'],
            [['apikey'], 'string'],
            [['apikey'], 'uniqApikey'],
        ];
    }

    /**
     * Validate apikey
     * @param $attribute
     */
    public function uniqApikey($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        if (Project::find()->andWhere('apikey = :apikey AND id <> :id', [
            ':apikey' => $this->$attribute,
            ':id' => $this->_project->id,
        ])->exists()) {
            $this->addError($attribute, Yii::t('app/superadmin', 'error.project.apikey_already_exist'));
            return false;
        }
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
        $this->site = $project->getSite();
        $this->attributes = $project->attributes;
        $this->captcha = !$project->captcha;
    }

    /**
     * Save project changes
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $isChangedCurrency = $isChangedCustomer = false;
        if ($this->currency != $this->_project->currency) {
            $isChangedCurrency = true;
        }

        if ($this->cid != $this->_project->cid) {
            $isChangedCustomer = true;
        }

        $this->_project->attributes = $this->attributes;
        $this->_project->captcha = !$this->captcha;

        if (!$this->_project->save(false)) {
            $this->addErrors($this->_project->getErrors());
            return false;
        }

        if ($isChangedCurrency) {
            $this->updateCurrencies();
        }

        if ($isChangedCustomer) {
            /**
             * @var $customer Customers
             */
            $customer = $this->getCustomers()[$this->cid];

            $customer->activateReferral();
            $customer->activateChildPanels();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'site' => Yii::t('app/superadmin', 'panels.edit.site'),
            'subdomain' => Yii::t('app/superadmin', 'panels.edit.subdomain'),
            'name' => Yii::t('app/superadmin', 'panels.edit.name'),
            'plan' => Yii::t('app/superadmin', 'panels.edit.plan'),
            'skype' => Yii::t('app/superadmin', 'panels.edit.skype'),
            'cid' => Yii::t('app/superadmin', 'panels.edit.customer'),
            'auto_order' => Yii::t('app/superadmin', 'panels.edit.auto_order'),
            'lang' => Yii::t('app/superadmin', 'panels.edit.lang'),
            'theme' => Yii::t('app/superadmin', 'panels.edit.theme'),
            'currency' => Yii::t('app/superadmin', 'panels.edit.currency'),
            'utc' => Yii::t('app/superadmin', 'panels.edit.utc'),
            'package' => Yii::t('app/superadmin', 'panels.edit.package'),
            'seo' => Yii::t('app/superadmin', 'panels.edit.seo'),
            'comments' => Yii::t('app/superadmin', 'panels.edit.comments'),
            'mentions_wo_hashtag' => Yii::t('app/superadmin', 'panels.edit.mentions_wo_hashtag'),
            'mentions' => Yii::t('app/superadmin', 'panels.edit.mentions'),
            'mentions_custom' => Yii::t('app/superadmin', 'panels.edit.mentions_custom'),
            'mentions_hashtag' => Yii::t('app/superadmin', 'panels.edit.mentions_hashtag'),
            'mentions_follower' => Yii::t('app/superadmin', 'panels.edit.mentions_follower'),
            'mentions_likes' => Yii::t('app/superadmin', 'panels.edit.mentions_likes'),
            'writing' => Yii::t('app/superadmin', 'panels.edit.writing'),
            'drip_feed' => Yii::t('app/superadmin', 'panels.edit.drip_feed'),
            'captcha' => Yii::t('app/superadmin', 'panels.edit.captcha'),
            'custom' => Yii::t('app/superadmin', 'panels.edit.custom'),
            'start_count' => Yii::t('app/superadmin', 'panels.edit.start_count'),
            'apikey' => Yii::t('app/superadmin', 'panels.edit.apikey'),
        ];
    }

    /**
     * Get plans
     * @return array
     */
    public function getPlans()
    {
        return ArrayHelper::map(Tariff::find()->all(), 'id', 'title');
    }

    /**
     * Get languages
     * @return mixed
     */
    public function getLanguages()
    {
        return Yii::$app->params['languages'];
    }

    /**
     * Get themes
     * @return mixed
     */
    public function getThemes()
    {
        return Yii::$app->params['themes'];
    }

    /**
     * Get currencies
     * @return mixed
     */
    public function getCurrencies()
    {
        $currencies = [];

        foreach (Yii::$app->params['currencies'] as $code => $currency) {
            $currencies[$currency['id']] = $currency['name'] . ' (' . $code . ')';
        }
        return $currencies;
    }

    /**
     * Get timezones
     * @return mixed
     */
    public function getTimezones()
    {
        return Yii::$app->params['timezones'];
    }

    /**
     * Get customers
     * @return Customers[]
     */
    public function getCustomers()
    {
        if (null !== $this->_customers) {
           return $this->_customers;
        }

        $this->_customers = ArrayHelper::index(Customers::find()->all(), 'id');

        return $this->_customers;
    }

    /**
     * Update payments geteway panel values
     */
    public function updateCurrencies()
    {
        PaymentGateway::updateAll([
            'position' => 0,
        ], 'pid = :pid', [
            ':pid' => $this->_project->id
        ]);

        $currencies = Yii::$app->params['currencies'];
        $currency = strtoupper($this->_project->getCurrencyCode());

        if (empty($currencies[$currency])) {
            return;
        }

        $gatewayMethods = ArrayHelper::index($currencies[$currency]['gateway'], 'position');
        ksort($gatewayMethods);

        $currentMethods = PaymentGateway::find()->andWhere([
            'pid' => $this->_project->id
        ])->all();
        $currentMethods = ArrayHelper::index($currentMethods, 'pgid');

        $position = 1;
        foreach ($gatewayMethods as $key => $options) {
            $pgid = $options['pgid'];
            if (empty($currentMethods[$pgid])) {
                continue;
            }

            unset($gatewayMethods[$key]);

            if (isset($options['allow']) && empty($options['allow'][$this->_project->id])) {
                continue;
            }

            $currentMethods[$pgid]->position = $position++;

            $currentMethods[$pgid]->save(false);
        }

        foreach ($gatewayMethods as $key => $options) {
            $model = new PaymentGateway();
            $model->attributes = $options;
            $model->pid = $this->_project->id;
            $model->setOptionsData([]);

            if (isset($options['allow']) && empty($options['allow'][$this->_project->id])) {
                $model->position = 0;
            } else {
                $model->position = $position++;
            }

            $model->save(false);
        }
    }
}