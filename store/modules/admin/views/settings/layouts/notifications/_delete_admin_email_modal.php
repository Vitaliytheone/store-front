<?php
    /* @var $this yii\web\View */
    /* @var $form \common\components\ActiveForm */

    use common\components\ActiveForm;
    use yii\bootstrap\Html;
    use store\modules\admin\components\Url;
?>
<div class="modal fade" id="deleteAdminEmailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <?php $form = ActiveForm::begin([
                            'id' => 'deleteAdminEmailForm',
                            'action' => Url::toRoute('/settings/delete-email'),
                            'options' => [
                                'class' => "form",
                            ],
                            'fieldClass' => 'yii\bootstrap\ActiveField',
                        ]); ?>
                            <span class="fa fa-trash-o"></span>
                            <p><?= Yii::t('admin', 'settings.confirm_delete_email') ?></p>
                            <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.notifications_cancel_btn') ?></button>
                            <?= Html::submitButton(Yii::t('admin', 'settings.notifications_confirm_btn'), [
                                'class' => 'btn btn-danger ml-2',
                                'name' => 'delete-admin-email-button',
                                'id' => 'deleteAdminEmailButton'
                            ]) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>