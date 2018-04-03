<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \my\modules\superadmin\models\forms\EditProjectForm */

use my\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$this->context->addModule('superadminEditPanelController');
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">

                <?php $form = ActiveForm::begin([
                    'id' => 'edit-panel-form',
                    'options' => [
                        'class' => "form",
                    ],
                    'fieldClass' => 'yii\bootstrap\ActiveField',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}",
                    ],
                ]);?>

                <div class="panel-body">
                    <?= $form->errorSummary($model); ?>

                    <div class="form-group">

                        <?= $form->field($model, 'site')->textInput([
                            'disabled' => 'disabled'
                        ]) ?>

                        <?= $form->field($model, 'subdomain')->checkbox() ?>

                        <?= $form->field($model, 'name') ?>

                        <?= $form->field($model, 'skype') ?>

                        <?= $form->field($model, 'plan')->dropDownList($model->getPlans()) ?>

                        <div class="form-group field-editprojectform-cid">
                            <label class="control-label" for="editprojectform-cid"><?= $model->getAttributeLabel('cid')?></label>
                            <select id="editprojectform-cid" class="form-control selectpicker" name="EditProjectForm[cid]" data-live-search="true">
                                <?php foreach ($model->getCustomers() as $customer) : ?>
                                    <option data-tokens="<?= $customer->email ?>" value="<?= $customer->id ?>" <?= ($customer->id == $model->cid ? 'selected' : '') ?>>
                                        <?= $customer->email ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?= $form->field($model, 'auto_order')->checkbox() ?>

                        <?= $form->field($model, 'lang')->dropDownList($model->getLanguages()) ?>

                        <?= $form->field($model, 'theme')->dropDownList($model->getThemes()) ?>

                        <?= $form->field($model, 'currency')->dropDownList($model->getCurrencies()) ?>

                        <?= $form->field($model, 'utc')->dropDownList($model->getTimezones()) ?>

                        <h3><?= Yii::t('app/superadmin', 'panels.edit.service_type_header')?></h3>

                        <?= $form->field($model, 'package')->checkbox() ?>

                        <?= $form->field($model, 'seo')->checkbox() ?>

                        <?= $form->field($model, 'comments')->checkbox() ?>

                        <?= $form->field($model, 'mentions_wo_hashtag')->checkbox() ?>

                        <?= $form->field($model, 'mentions')->checkbox() ?>

                        <?= $form->field($model, 'mentions_custom')->checkbox() ?>

                        <?= $form->field($model, 'mentions_hashtag')->checkbox() ?>

                        <?= $form->field($model, 'mentions_follower')->checkbox() ?>

                        <?= $form->field($model, 'mentions_likes')->checkbox() ?>

                        <?= $form->field($model, 'writing')->checkbox() ?>

                        <?= $form->field($model, 'drip_feed')->checkbox() ?>

                        <h3><?= Yii::t('app/superadmin', 'panels.edit.advanced_header')?></h3>

                        <?= $form->field($model, 'captcha')->checkbox() ?>

                        <?= $form->field($model, 'name_modal')->checkbox() ?>

                        <?= $form->field($model, 'custom')->checkbox() ?>

                        <?= $form->field($model, 'start_count')->checkbox() ?>

                        <div class="form-group field-editprojectform-apikey">
                            <label for="editprojectform-apikey" class="mr-sm-2">
                                <?= $model->getAttributeLabel('apikey')?>
                            </label>
                            <a class="btn btn-secondary pointer" id="generateApikey" href="<?= Url::toRoute('/panels/generate-apikey')?>">Generate</a>
                            <span class="btn btn-secondary pointer copy" data-clipboard-target="#editprojectform-apikey">Copy key</span>

                            <br />
                            <br />

                            <?= Html::input('text', 'EditProjectForm[apikey]', $model->apikey, [
                                'id' => 'editprojectform-apikey',
                                'class' => 'form-control'
                            ])?>
                        </div>
                    </div>
                </div>

                <div class="panel-footer" style="background-color: #fff">
                    <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app/superadmin', 'panels.edit.btn_submit')?></button>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>