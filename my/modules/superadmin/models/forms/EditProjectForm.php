<?php
namespace superadmin\models\forms;

use common\models\panel\Bonuses;
use common\models\panels\AdditionalServices;
use common\models\panels\Customers;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethodsCurrency;
use common\models\panels\Tariff;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class EditProjectForm
 * @package superadmin\models\forms
 */
class EditProjectForm extends Model
{

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
    public $name_modal;
    public $custom;
    public $start_count;
    public $apikey;
    public $no_invoice;
    public $currency_code;

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
                'name_modal',
                'custom',
                'start_count',
                'apikey',
                'no_invoice'
            ], 'safe'],
            ['cid', 'checkOwnedChildPanel'],
            [['apikey'], 'string'],
            [['apikey'], 'uniqApikey'],
        ];
    }

    /**
     * @return array
     */
    public function getInputAttrs()
    {
        return [
            'site',
            'name',
            'skype',
            'apikey'
        ];
    }

    /**
     * @return array
     */
    public function getDropDownAttrs()
    {
        return [
            'currency',
            'plan',
            'utc',
            'cid'
        ];
    }

    /**
     * @return array
     */
    public function getServiceTypes()
    {
        return [
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
        ];
    }

    /**
     * @return array
     */
    public function getAdvanced()
    {
        return [
            'captcha',
            'name_modal',
            'no_invoice',
            'custom',
            'start_count'
        ];
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return [
            'checkboxes' => array_merge(
                ['subdomain'],
                $this->getAdvanced(),
                $this->getServiceTypes()
            ),
            'dropdowns' => $this->getDropDownAttrs(),
            'textInputs' => $this->getInputAttrs()
        ];
    }

    /**
     * Validate apikey
     * @param $attribute
     * @return bool
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
     * Check panel to owned of child panel
     * @param string
     */
    public function checkOwnedChildPanel($attribute)
    {
        $childPanels = $this->_project->getChildPanels();

        foreach ($childPanels as $panel) {
            if (
                $this->cid != $this->_project->cid
                && ($panel->act === Project::STATUS_ACTIVE || $panel->act === Project::STATUS_FROZEN)
            ) {
                $this->addError($attribute, Yii::t('app/superadmin', 'panels.edit.error_have_active_cp'));
            }
        }
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

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $isChangedCurrency = $isChangedCustomer = $isChangedNoInvoice = false;
            if ($this->currency != $this->_project->currency) {
                $isChangedCurrency = true;
            }

            if ($this->cid != $this->_project->cid) {
                $isChangedCustomer = true;
            }

            if ($this->no_invoice != $this->_project->no_invoice) {
                $isChangedNoInvoice = true;
            }

            $this->_project->attributes = $this->attributes;
            $this->_project->captcha = !$this->captcha;

            if (!$this->_project->save(false)) {
                $this->addErrors($this->_project->getErrors());
                $transaction->rollBack();
                return false;
            }

            $this->_project->refresh();

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

            if ($isChangedNoInvoice && Project::NO_INVOICE_ENABLED == $this->_project->no_invoice) {
                /**
                 * @var Invoices $invoice
                 */
                foreach (Invoices::find()
                             ->joinWith(['invoiceDetails'])
                             ->andWhere([
                                 'invoices.status' => Invoices::STATUS_UNPAID,
                                 'invoice_details.item_id' => $this->_project->id,
                                 'invoice_details.item' => [
                                     InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL,
                                     InvoiceDetails::ITEM_PROLONGATION_PANEL,
                                 ],
                             ])
                             ->all() as $invoice) {
                    $invoice->status = Invoices::STATUS_CANCELED;
                    $invoice->save(false);
                }
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

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
            'name_modal' => Yii::t('app/superadmin', 'panels.edit.name_modal'),
            'custom' => Yii::t('app/superadmin', 'panels.edit.custom'),
            'start_count' => Yii::t('app/superadmin', 'panels.edit.start_count'),
            'apikey' => Yii::t('app/superadmin', 'panels.edit.apikey'),
            'no_invoice' => Yii::t('app/superadmin', 'panels.edit.no_invoice'),
        ];
    }

    /**
     * Get plans
     * @return array
     */
    public function getPlans()
    {
        return ArrayHelper::map(
            Tariff::find()
               ->where([
                    '>=',
                    'id', 0,
                ])
                ->orderBy('id ASC')
                ->all(), 'id', 'title');
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
     * @param int|null $limit
     * @return array|Customers[]
     */
    public function getCustomers()
    {
        if (null !== $this->_customers) {
           return $this->_customers;
        }

        $query = Customers::find();

        $this->_customers = ArrayHelper::index($query->all(), 'id');

        return $this->_customers;
    }

    /**
     * Update panel payment methods
     */
    public function updateCurrencies()
    {
        $currency = $this->_project->getCurrencyCode();
        $panelDb = $this->_project->db;
        $db = Yii::$app->db;

        $currentPaymentMethods = PanelPaymentMethods::find()->andWhere([
            'panel_id' => $this->_project->id
        ])->indexBy('method_id')->all();

        $availablePaymentMethods = PaymentMethodsCurrency::find()->andWhere([
            'currency' => $currency,
        ])->indexBy('method_id')->all();

        foreach ($currentPaymentMethods as $methodId => $currentPaymentMethod) {
            if (empty($availablePaymentMethods[$methodId])) {

                // Disable panel payment method bonus
                $db->createCommand()->update($db->quoteValue($panelDb) . '.' . Bonuses::tableName(), [
                    'status' => Bonuses::STATUS_DISABLED
                ], [
                    'pgid' => $currentPaymentMethod->method_id
                ])->execute();

                $currentPaymentMethod->delete();
                continue;
            }

            $currentPaymentMethod->currency_id = $availablePaymentMethods[$methodId]->id;
            $currentPaymentMethod->save(false);
        }

        AdditionalServices::updateAll(
            ['currency' => $currency],
            ['type' => 1, 'name' => $this->_project->site]
        );
    }
}