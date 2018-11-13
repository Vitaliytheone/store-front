<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\OrderPanelForm */

    use my\helpers\Url;
?>
<div class="modal fade" tabindex="-1" role="dialog" id="orderDomainModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'panels.order.registrant_modal_header')?></h4>
            </div>
                <div class="form-horizontal">
                    <div class="modal-body">

                        <?= $form->errorSummary($model, [
                            'id' => 'orderDomainError'
                        ]); ?>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_email') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_email')->textInput([
                                    'id' => 'modal_domain_email',
                                    'class' => 'form-control'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_firstname') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_firstname')->textInput([
                                    'id' => 'modal_domain_firstname'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_lastname') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_lastname')->textInput([
                                    'id' => 'modal_domain_lastname'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?= $model->getAttributeLabel('domain_company') ?></label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_company')->textInput([
                                    'id' => 'modal_domain_company'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_address') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_address')->textInput([
                                    'id' => 'modal_domain_address'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_city') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_city')->textInput([
                                    'id' => 'modal_domain_city'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_postalcode') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_postalcode')->textInput([
                                    'id' => 'modal_domain_postalcode'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_state') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_state')->textInput([
                                    'id' => 'modal_domain_state'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_country') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_country')->dropDownList($model->getCountries(), [
                                    'id' => 'modal_domain_country',
                                    'prompt' => ''
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_phone') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_phone')->textInput([
                                    'id' => 'modal_domain_phone'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?= $model->getAttributeLabel('domain_fax') ?>
                            </label>

                            <div class="col-sm-9">
                                <?= $form->field($model, 'domain_fax')->textInput([
                                    'id' => 'modal_domain_fax'
                                ])->label(false)?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <?= $form->field($model, 'domain_protection')->checkbox()?>
                            </div>
                        </div>

                        <?= $form->field($model, 'domain_name')->hiddenInput([
                            'id' => 'modal_domain_name',
                            'class' => 'form-control'
                        ])->label(false)?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'panels.order.registrant_modal_btn_close') ?></button>
                        <button type="submit" class="btn btn-primary has-spinner" id="orderDomainBtn" data-action="<?= Url::toRoute(['/domains/order-domain', 'order' => 'panel']) ?>">
                            <span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>
                            <?= Yii::t('app', 'panels.order.registrant_modal_btn_submit') ?>
                        </button>
                    </div>

                </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->