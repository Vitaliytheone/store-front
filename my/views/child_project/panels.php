<?php
    /* @var $this yii\web\View */
    /* @var $panels[] \common\models\panels\Project */
    /* @var $panel \common\models\panels\Project */
    /* @var $note */
    /* @var $accesses */

    use common\models\panels\Project;
    use common\models\panels\Orders;
    use yii\bootstrap\Html;

    $projectColors = [
        Project::STATUS_FROZEN => 'text-danger',
        Project::STATUS_ACTIVE => 'text-success',
        Project::STATUS_TERMINATED => 'text-muted',
    ];

    $orderColors = [
        Orders::STATUS_PAID => '',
        Orders::STATUS_ERROR => '',
        Orders::STATUS_PENDING => '',
        Orders::STATUS_CANCELED => 'text-muted',
    ];

    $colors = function($panel) use ($projectColors, $orderColors) {
        if ('order' == $panel['type']) {
            return $orderColors[$panel['status']];
        } else {
            return $projectColors[$panel['status']];
        }
    };

    $this->context->addModule('panelsController');
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">
            <?= Yii::t('app', 'child_panels.list.header')?>
            <a href="/childpanels/order" class="btn btn-outline btn-success create-order" <?= $accesses['canCreate'] ? '' : 'data-error="' . Yii::t('app', 'child_panels.order_can_not_create') . '"' ?>>
                <?= Yii::t('app', 'child_panels.list.order_panel')?>
            </a>
            <div class="alert alert-danger error-hint hidden" role="alert" style="margin-top: 10px">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="content"></span>
            </div>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-8">

        <div class="manual-panel">
            <?= (string)$note ?>
        </div>

    </div>
</div>
<?php if (!empty($panels)): ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'child_panels.list.column_domain')?></th>
                        <th><?= Yii::t('app', 'child_panels.list.column_provider')?></th>
                        <th><?= Yii::t('app', 'child_panels.list.column_created')?></th>
                        <th><?= Yii::t('app', 'child_panels.list.column_expiry')?></th>
                        <th><?= Yii::t('app', 'child_panels.list.order_status')?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panels as $panel): ?>
                    <tr>
                        <td><?= $panel['domain'] ?></td>
                        <td><?= $panel['provider']; ?></td>
                        <td>
                            <?= $panel['date']; ?>
                        </td>
                        <td>
                            <?= $panel['expired']; ?>
                        </td>
                        <td class="<?= $colors($panel) ?>">
                            <?= $panel['statusName'] ?>
                        </td>
                        <td>
                            <?php if ($panel['access']['isActive']) : ?>
                                <?= Html::a('<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_dashboard'), 'http://'. strip_tags($panel['domain']) . '/admin', [
                                    'class' => 'btn btn-outline btn-primary btn-xs',
                                    'target' => '_blank'
                                ])?>
                                <?= Html::a('<i class="fa fa-user fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_staff'), '/childpanels/staff/' . $panel['id'], [
                                    'class' => 'btn btn-outline btn-info btn-xs',
                                ])?>
                                <?php /*
                                <?= Html::a('<i class="fa fa fa-globe fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_domain'), '/childpanels/change-domain/' . $panel['id'], [
                                    'class' => 'btn btn-outline btn-info btn-xs',
                                ])?>
                                */ ?>
                            <?php else : ?>
                                <?= Html::tag('span', '<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_dashboard'), [
                                    'class' => 'btn btn-outline btn-primary btn-xs disabled',
                                    'target' => '_blank'
                                ])?>
                                <?= Html::tag('span', '<i class="fa fa-user fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_staff'), [
                                    'class' => 'btn btn-outline btn-info btn-xs disabled',
                                ])?>
                                <?php /*
                                <?= Html::tag('span', '<i class="fa fa fa-globe fa-fw"></i> ' . Yii::t('app', 'child_panels.list.action_domain'), [
                                    'class' => 'btn btn-outline btn-info btn-xs disabled',
                                ])?>
                                */ ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
