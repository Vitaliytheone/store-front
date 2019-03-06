<?php
    /* @var $this \yii\web\View */
    /* @var $providers \sommerce\modules\admin\models\search\ProvidersSearch */

use common\components\ActiveForm;
use yii\bootstrap\Html;

$model = new \sommerce\modules\admin\models\forms\ProvidersListForm();
?>
<?php $form = ActiveForm::begin([
    'id' => 'providersListForm',
]); ?>

<?php if (!empty($providers['models'])) : ?>
    <?php foreach ($providers['models'] as $key => $provider) : ?>
        <div class="form-group">
            <?= $form->field($model, 'id')->hiddenInput([
                'value' => $provider['id'],
                'name' => 'ProvidersListForm[providers][' . $key . '][key]'
            ])->label(false) ?>

            <?= $form->field($model, 'api_key')->textInput([
                'value' => $provider['apikey'],
                'name' => 'ProvidersListForm[providers][' . $key . '][api_key]'
            ])->label($provider['site'] . ' API key') ?>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p><?= Yii::t('admin', 'settings.providers_no_providers') ?></p>
<?php endif; ?>
<hr>
<?= Html::submitButton(Yii::t('admin', 'settings.providers_save'), ['class' => 'btn btn-success m-btn--air', 'name' => 'save-button']) ?>
<?php ActiveForm::end(); ?>