<?php
use admin\components\Url;
use common\components\ActiveForm;

/* @var $model \admin\models\forms\EditPaymentMethodForm; */

$formData = $model->getDetails();
$method = $model->method_id;
$submitUrl = Url::toRoute(['/settings/payments-settings', 'method' => $method]);
$cancelUrl = Url::toRoute(['/settings/payments']);
?>

<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', "settings.payments_edit_$method") ?>
            </h3>
        </div>
    </div>
</div>

<div class="m-content">

    <div class="gateway-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $model->getPaymentMethod()->icon ?>" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <?= $this->render('_rules', ['method' => $model->method_id]);?>
            </div>
        </div>
    </div>

        <?php $form = ActiveForm::begin([
            'id' => 'paypalSettingsForm',
            'action' => $submitUrl,
            'fieldClass' => 'yii\bootstrap\ActiveField',
            'fieldConfig' => [
                'template' => "{label}\n{input}",
            ],
        ]); ?>

        <?php foreach ($formData as $formField): ?>

            <?php if($formField['type'] == 'text'): ?>
                <div class="form-group">
                    <label for="<?= $formField['id'] ?>">
                        <?= $formField['label'] ?>
                    </label>
                    <input type="text" class="form-control" id="<?= $formField['id'] ?>" placeholder="<?= $formField['placeholder'] ?>" name="<?= $formField['name'] ?>" value="<?= $formField['value'] ?>">
                </div>
            <?php endif; ?>

            <?php if($formField['type'] == 'checkbox'): ?>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="hidden" name="<?= $formField['name'] ?>" value="0">
                        <input type="checkbox" class="form-check-input" name="<?= $formField['name'] ?>" value="1" <?= (!empty($formField['value']) ? 'checked="checked"' : '') ?> >
                        <?= $formField['label'] ?>
                    </label>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
        <hr>

        <button type="submit" class="btn btn-success">
                <?= Yii::t('admin', 'settings.payments_save_method') ?>
        </button>
        <a href="<?= $cancelUrl ?>" class="btn btn-secondary">
                <?= Yii::t('admin', 'settings.payments_cancel_method') ?>
        </a>

        <?php ActiveForm::end(); ?>
</div>

