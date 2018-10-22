<?php

use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $panels array */
/* @var $statusButtons */
/* @var $status */
/* @var $panelType integer */

?>

    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-pills mb-3" role="tablist">
                <?php foreach ($statusButtons as $button) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($button['status'] == $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/tools/' . $this->context->action->id, 'status' => $button['status']]) ?>"><?= $button['title'] ?> (<?= $button['count'] ?>)</a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-4">
            <div class="pull-right">
                <button type="button" class="btn btn-primary btn-outline-primary" data-toggle="modal" data-target="#add_domain_modal">
                    <?= Yii::t('app/superadmin', 'tools.levopanel.list.add_domain') ?>
                </button>
            </div>
        </div>
    </div>

    <?= $this->render('layouts/panels_scanner/_panel_list', [
        'panels' => $panels
    ])?>



<?= $this->render('layouts/panels_scanner/_add_domain_modal', [
    'panels' => $panels,
    'panelType' => $panelType,
])?>
