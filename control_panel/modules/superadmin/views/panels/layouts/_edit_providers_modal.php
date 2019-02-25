<?php
    /* @var $this yii\web\View */
    /* @var $form \control_panel\components\ActiveForm */
    /* @var $modal \superadmin\models\forms\EditProvidersForm */
    /* @var $action string */

    use control_panel\components\ActiveForm;
    use control_panel\helpers\Url;
    use yii\bootstrap\Html;

    $model = new superadmin\models\forms\EditProvidersForm();
?>
<div class="modal fade" id="editProvidersModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.edit.providers') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body max-height-400">
                <div class="card-custom">
                    <div class="card-custom__title"><?= Yii::t('app/superadmin', 'panels.edit.filters') ?></div>
                    <div class="form-group">
                        <div class="d-flex mb-1">
                            <div class="custom-control custom-checkbox custom-checkbox-filter">
                                <input type="checkbox" class="custom-control-input" id="show-selected-checkbox">
                                <label class="custom-control-label" for="show-selected-checkbox"><?= Yii::t('app/superadmin', 'panels.edit.show_selected') ?></label>
                            </div>
                            <div class="custom-control custom-checkbox custom-checkbox-filter">
                                <input type="checkbox" class="custom-control-input" id="perfect-panel-checkbox">
                                <label class="custom-control-label" for="perfect-panel-checkbox"><?= Yii::t('app/superadmin', 'panels.edit.perfect_panel_providers') ?> <span class="fa fa-check-circle-o"></span></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="text" id="search-providers" class="form-control" placeholder="<?= Yii::t('app/superadmin', 'panels.edit.search') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-light" id="modal-search-providers" type="button"><span class="fa fa-search"></span></button>
                    </div>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'editProvidersForm',
                    'action' => Url::toRoute("/$action/edit-providers"),
                    'options' => [
                        'class' => "form",
                    ],
                    'fieldClass' => 'yii\bootstrap\ActiveField',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}",
                    ],
                ]); ?>

                <?= $form->errorSummary($model, [
                    'id' => 'editProvidersError'
                ]); ?>

                <div class="providers-filter-result">
                    <input type="hidden" name="EditProvidersForm[providers]" value="">
                    <?php $i = 1 ?>
                    <?php foreach($model->getProviders() as $id => $provider) :  ?>

                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="provider-id-<?= $i ?>" type="checkbox" name="EditProvidersForm[providers][]" value="<?= $id ?>">
                        <label class="custom-control-label" for="provider-id-<?= $i ?>">
                            <?= $provider['name'] ?>
                        </label>
                        <?php if ($provider['internal']) : ?>
                            &nbsp;<span class="fa fa-check-circle-o"></span>
                        <?php endif ?>
                    </div>
                    <?php $i++ ?>
                <?php endforeach; ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.edit.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.save'), [
                    'class' => 'btn  btn-primary',
                    'name' => 'edit-providers-button',
                    'id' => 'editProvidersButton',
                    'data-dismiss' => 'modal'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>