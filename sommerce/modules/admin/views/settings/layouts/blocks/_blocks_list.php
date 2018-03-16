<?php
    /* @var $this \yii\web\View */
    /* @var $blocks \yii\web\View */

    use sommerce\modules\admin\components\Url;
    use yii\bootstrap\Html;
?>

<?php foreach ($blocks as $block) : ?>
    <div class="sommerce-card__block m-portlet">
        <div class="row align-items-center">
            <div class="col-6">
                <div class="card-block__title">
                    <?= $block['label'] ?>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-end">
                    <div class="card-block__switch">
                    <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                        <label>
                            <?= Html::checkbox($block['code'], $block['active'], [
                                'class' => 'change-status',
                                'data-enable' => Url::toRoute(['/settings/enable-block', 'code' => $block['code']]),
                                'data-disable' => Url::toRoute(['/settings/disable-block', 'code' => $block['code']]),
                            ])?>
                            <span></span>
                        </label>
                    </span>
                    </div>
                    <div class="card-block__actions">
                        <a href="<?= Url::toRoute(['/settings/edit-block', 'code' => $block['code']]) ?>" class="btn m-btn--pill m-btn--air btn-primary">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>