<?php

use control_panel\components\ActiveForm;
use control_panel\helpers\Url;

/* @var $this yii\web\View */
/* @var $datetime array */
/* @var $statuses array */
?>

<div class="dropdown statuses-filter__dropdown">
    <button class="btn btn-light statuses-filter" data-toggle="dropdown" type="button" id="dropdownMenuButton">
        <?= $datetime['from'] . ' - ' . $datetime['to'] ?> <span class="fa fa-calendar"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php $form = ActiveForm::begin([
            'id' => 'createTicketForm',
            'method' => 'get',
            'action' => Url::toRoute(['/statuses/getstatus']),
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
                    <?= $this->render('layouts/getstatus/_getstatus_list', [
                            'statuses' => $statuses,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
