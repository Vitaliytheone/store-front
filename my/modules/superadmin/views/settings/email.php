<?php
/* @var $this yii\web\View */
/* @var $emails \superadmin\models\search\NotificationEmailSearch */


$this->context->addModule('superadminEmailSettingsController');
?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <?= $this->render('layouts/_menu', ['mailsActive' => 'active']); ?>
            </div>
        </div>
        <div class="col-lg-9">
            <?= $this->render('layouts/_emails_list', [
                'emails' => $emails
            ]) ?>
        </div>
    </div>
</div>

<?= $this->render('layouts/_edit_email_modal'); ?>
