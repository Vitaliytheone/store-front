<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\store\NotificationAdminEmails;
use common\models\store\NotificationTemplates;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditAdminEmailForm;
use sommerce\modules\admin\models\forms\EditNotificationForm;
use sommerce\modules\admin\models\forms\SendTestNotificationForm;
use sommerce\modules\admin\models\forms\TestNotificationForm;
use sommerce\modules\admin\models\search\NotificationsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Trait NotificationsTrait
 * @package sommerce\modules\admin\controllers\traits\settings
 */
trait NotificationsTrait {

    /**
     * @var NotificationDefaultTemplates|null
     */
    protected $_defaultTemplate;

    /**
     * @var NotificationTemplates|null
     */
    protected $_storeTemplate;

    /**
     * Return available notifications for current store
     * @return mixed
     */
    public function actionNotifications()
    {
        $this->view->title = Yii::t('admin', 'settings.notifications_page_title');

        $this->addModule('adminNotifications');

        $langSearch = new NotificationsSearch();
        $langSearch->setStore(Yii::$app->store->getInstance());

        return $this->render('notifications', [
            'notifications' => $langSearch->getNotifications(),
            'emails' => $langSearch->getEmails(),
        ]);
    }

    /**
     * Edit notification action
     * @param string $code
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditNotification($code)
    {
        $this->view->title = Yii::t('admin', 'settings.notification_edit_page_title', [
            'name' => Yii::t('admin', 'notifications.label.' . $code)
        ]);

        $notification = $this->_findNotification($code, true);

        $model = new EditNotificationForm();
        $model->setNotification($notification);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.notification_has_been_updated'));
            return $this->refresh();
        }

        $this->addModule('adminEditNotification');

        return $this->render('edit_notification', [
            'model' => $model,
        ]);
    }

    /**
     * Reset notification action
     * @param string $code
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionResetNotification($code)
    {
        $notification = $this->_findNotification($code, true);

        $notification->subject = $this->_defaultTemplate->subject;
        $notification->body = $this->_defaultTemplate->body;
        $notification->save(false);

        UiHelper::message(Yii::t('admin', 'settings.notification_has_been_updated'));

        return $this->redirect(Url::toRoute(['/settings/edit-notification', 'code' => $code]));
    }

    /**
     * Enable/disable notification
     * @param string $code
     * @param integer $status
     * @return array
     */
    public function actionChangeNotificationStatus($code, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $notification = $this->_findNotification($code);

        if (!in_array($status, array_keys(NotificationTemplates::getStatuses()))) {
            return [
                'status' => 'error'
            ];
        }

        $notification->status = $status;
        $notification->save(false);

        return [
            'status' => 'success'
        ];
    }

    /**
     * Enable/disable email
     * @param integer $id
     * @param integer $status
     * @return array
     */
    public function actionChangeEmailStatus($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $email = $this->_findEmail($id);

        if (!in_array($status, array_keys(NotificationAdminEmails::getStatuses()))) {
            return [
                'status' => 'error'
            ];
        }

        $email->status = $status;
        $email->save(false);

        return [
            'status' => 'success'
        ];
    }

    /**
     * Create email
     *
     * @access public
     * @return mixed
     */
    public function actionCreateEmail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EditAdminEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_admin_email_created'));
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Edit email
     *
     * @access public
     * @@param integer $id
     * @return mixed
     */
    public function actionEditEmail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $email = $this->_findEmail($id);

        $model = new EditAdminEmailForm();
        $model->setEmail($email);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_admin_email_updated'));
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Delete email
     *
     * @access public
     * @@param integer $id
     * @return mixed
     */
    public function actionDeleteEmail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $email = $this->_findEmail($id);

        if ($email->primary) {
            return [
                'status' => 'error',
            ];
        }

        $email->delete();

        return [
            'status' => 'success',
        ];
    }

    /**
     * Render notification preview
     * @param string $code
     * @return string
     */
    public function actionNotificationPreview($code)
    {
        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();
        $notification = $this->_findNotification($code, true);

        $testMail = new TestNotificationForm();
        $testMail->setNotification($notification);
        $testMail->setStore($store);

        return $testMail->message();
    }

    /**
     * Send test notification email
     * @param string $code
     * @return array
     */
    public function actionSendTestNotification($code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();
        $notification = $this->_findNotification($code, true);

        $model = new SendTestNotificationForm();
        $model->setNotification($notification);
        $model->setStore($store);

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            return [
                'status' => 'success',
                'message' => Yii::t('admin', 'settings.message_send_test_email_success')
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Find notification
     * @param string $code
     * @param boolean $content - use content or not
     * @return NotificationTemplates|null
     * @throws NotFoundHttpException
     */
    protected function _findNotification($code, $content = false)
    {
        $this->_defaultTemplate = NotificationDefaultTemplates::findOne([
            'code' => $code
        ]);

        if (!$this->_defaultTemplate) {
            throw new NotFoundHttpException();
        }

        $this->_storeTemplate = NotificationTemplates::findOne([
            'notification_code' => $code
        ]);

        if (!$this->_storeTemplate) {
            $this->_storeTemplate = new NotificationTemplates();
            $this->_storeTemplate->notification_code = $code;
            $this->_storeTemplate->status = $this->_defaultTemplate->status;

            if ($content) {
                $this->_storeTemplate->subject = $this->_defaultTemplate->subject;
                $this->_storeTemplate->body = $this->_defaultTemplate->body;
            }
        }

        if (null == $this->_storeTemplate->subject) {
            $this->_storeTemplate->subject = $this->_defaultTemplate->subject;
        }

        if (null == $this->_storeTemplate->body) {
            $this->_storeTemplate->body = $this->_defaultTemplate->body;
        }

        return $this->_storeTemplate;
    }

    /**
     * Find email
     * @param integer $id
     * @return NotificationAdminEmails|null
     * @throws NotFoundHttpException
     */
    protected function _findEmail($id)
    {
        $email = NotificationAdminEmails::findOne($id);

        if (!$email) {
            throw new NotFoundHttpException();
        }

        return $email;
    }
}