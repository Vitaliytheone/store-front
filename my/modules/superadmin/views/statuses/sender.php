<?php

use my\components\ActiveForm;
use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $datetime array */
/* @var $senders array */
?>

<div class="dropdown statuses-filter__dropdown">
    <button class="btn btn-light statuses-filter" data-toggle="dropdown" type="button" id="dropdownMenuButton">
        <?= $datetime['from'] . ' - ' . $datetime['to'] ?> <span class="fa fa-calendar"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php $form = ActiveForm::begin([
            'id' => 'createTicketForm',
            'method' => 'get',
            'action' => Url::toRoute('/statuses/sender'),
            'fieldConfig' => [
                'template' => "{label}\n{input}",
                'labelOptions' => ['class' => 'control-label'],
            ],
        ]); ?>
        <div class="statuses-filter__block">
            <label><?= Yii::t('app/superadmin', 'statuses.datetimepicker.from') ?></label>
            <input type="text" class="form-control" name="from" value="<?= $datetime['from'] ?>">
            <label><?= Yii::t('app/superadmin', 'statuses.datetimepicker.to') ?></label>
            <input type="text" name="to" class="form-control" value="<?= $datetime['to'] ?>">
            <button type="submit" class="btn btn-primary"><?= Yii::t('app/superadmin', 'statuses.datetimepicker.submit_btn') ?></button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane fade show active" id="status-all" role="tabpanel">
        <div id="data-table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <?= $this->render('layouts/sender/_sender_list', [
                            'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
