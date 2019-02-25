<?php

use control_panel\components\ActiveForm;

/* @var $this yii\web\View */
/* @var $datetime array */
/* @var $statuses array */
?>

<div class="tab-content">
    <div class="tab-pane fade show active" id="status-all" role="tabpanel">
        <div id="data-table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <?= $this->render('layouts/subscription/_subscription_list', [
                            'model' => $model
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
