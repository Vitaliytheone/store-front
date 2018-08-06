<?php

namespace my\controllers;

use my\helpers\ReferralHelper;
use common\models\panels\Content;
use common\models\panels\Customers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;


/**
 * Class ReferralsController
 * @package my\controllers
 */
class ReferralsController extends CustomController
{
    /**
     * @var Customers
     */
    protected $_customer;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }
                            
                            /**
                             * @var $customer Customers
                             */
                            $this->_customer = Yii::$app->user->getIdentity();

                            if (!$this->_customer || !$this->_customer->can('referral')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                    [
                        'actions' => ['ref'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (!Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }
                            $code = Yii::$app->request->get('code');

                            if (!$code || !($this->_customer = Customers::findOne([
                                'referral_link' => $code,
                            ]))) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            if (!$this->_customer->can('referral')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                ],

            ],
        ];
    }

    /**
     * View referral information
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app', 'pages.title.referral');

        $totalVisits = count($this->_customer->referralVisits);
        $unpaidReferrals = count($this->_customer->unpaidReferrals);
        $paidReferrals = count($this->_customer->paidReferrals);
        $conversionRate = (($paidReferrals && $totalVisits) ? (($paidReferrals * 100)/ $totalVisits) : 0);

        return $this->render('index', [
            'customer' => $this->_customer,
            'note' => Content::getContent('referral'),
            'referral' => [
                'total_visits' => $totalVisits,
                'unpaid_referrals' => $unpaidReferrals,
                'paid_referrals' => $paidReferrals,
                'conversion_rate' => $conversionRate,
                'total_earnings' => (float)$this->_customer->totalEarnings,
                'unpaid_earnings' => (float)$this->_customer->getUnpaidEarnings(),
            ]
        ]);
    }

    /**
     * Process ref link
     * @return Response
     */
    public function actionRef()
    {
        ReferralHelper::visit($this->_customer);

        $this->redirect('/');
    }
}
