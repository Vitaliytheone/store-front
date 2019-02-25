<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \control_panel\models\forms\CreateChildForm */

use control_panel\helpers\Url;
use control_panel\helpers\DomainsHelper;
?>

<div class="panel-body">
    <div class="form-group">
        <div class="input-group">
            <?= $form->field($model, 'search_domain')->textInput([
                'id' => 'searchDomain',
                'placeholder' => $model->getAttributeLabel('search_domain')
            ])->label(false)?>

            <div class="input-group-btn">
                <?= $form->field($model, 'domain_zone')->dropDownList(DomainsHelper::getDomainZones(), [
                    'class' => 'selectpicker',
                    'id' => 'domain_zone'
                ])?>
            </div>

            <?= $form->field($model, 'domain_name')->hiddenInput([
                'id' => 'modal_domain_name',
                'class' => 'form-control'
            ])->label(false)?>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary has-spinner" type="button" id="searchDomainSubmit" data-action="<?= Url::toRoute('/search-domains') ?>"><span class="spinner"><i class="fa fa-spinner fa-spin"></i></span> <?= Yii::t('app', 'panels.order.btn_domain_search')?></button>
    </div>

    <div id="searchResult" class="hidden">
        <div id="searchResultContainer"></div>

        <div class="input-group">
            <button type="button" class="btn btn-primary disabled" id="continueDomainSearch" data-action="<?= Url::toRoute('childpanels/order-domain') ?>"><?= Yii::t('app', 'panels.order.btn_continue_domain_search')?></button>
        </div>

    </div>
</div>
