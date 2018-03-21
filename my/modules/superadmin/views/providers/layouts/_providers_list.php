<?php
    /* @var $this yii\web\View */
    /* @var $providers \my\modules\superadmin\models\search\ProvidersSearch */
    /* @var $provider \common\models\panels\AdditionalServices */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use common\models\panels\AdditionalServices;
?>
<table class="table table-border tablesorter-bootstrap" id="providersTable">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_name')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_count')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_in_use')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_start_count')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_refill')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_cancel')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_autolist')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_sender')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_type')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_status')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_created')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_skype')?></th>
        <th class="w-1"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($providers['models'])) : ?>
        <?php foreach ($providers['models'] as $provider) : ?>
            <?php
                $count = count($provider['projects']);
                $use = count($provider['usedProjects']);
            ?>
            <tr>
                <td>
                    <?= $provider['res'] ?>
                </td>
                <td>
                    <?= $provider['name'] ?>
                </td>
                <td>
                    <?php if ($count) : ?>
                        <?= Html::a($count, Url::toRoute(['/providers/get-panels', 'id' => $provider['id']]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['projects']),
                            'data-header' => $provider['name'] . ' - count'
                        ])?>
                    <?php else : ?>
                        <?= $count ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($use) : ?>
                        <?= Html::a($use, Url::toRoute(['/providers/get-panels', 'id' => $provider['id'], 'use' => 1]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['usedProjects']),
                            'data-header' => $provider['name'] . ' - in use'
                        ])?>
                    <?php else : ?>
                        <?= $use ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $provider['sc'] ?>
                </td>
                <td>
                    <?= $provider['refill'] ?>
                </td>
                <td>
                    <?= $provider['cancel'] ?>
                </td>
                <td>
                    <?= $provider['auto_services'] ?>
                </td>
                <td>
                    <?= $provider['auto_order'] ?>
                </td>
                <td>
                    <?= $provider['type'] ?>
                </td>
                <td>
                    <?= $provider['statusName'] ?>
                </td>
                <td>
                    <?= $provider['date'] > 0 ? $provider['date'] : '' ?>
                </td>
                <td>
                    <?= $provider['skype'] ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'providers.list.actions_label')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(Yii::t('app/superadmin', 'providers.list.action_edit'), Url::toRoute(['/providers/edit', 'id' => $provider['id']]), [
                                'class' => 'dropdown-item edit-provider',
                                'data-details' => Json::encode([
                                    'skype' => $provider['skype'],
                                    'name' => $provider['name']
                                ]),
                            ])?>
                            <h6 class="dropdown-header"><?= Yii::t('app/superadmin', 'providers.list.action_change_status') ?></h6>

                            <?php if (AdditionalServices::STATUS_ACTIVE != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.ok'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_ACTIVE
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_FROZEN != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.broken'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_FROZEN
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_PROCESSING != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.send_only'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_PROCESSING
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_NOT_UPDATED != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.not_updated'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_NOT_UPDATED
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>