<?php
    /* @var $this \yii\web\View */
    /* @var $providers \frontend\modules\admin\models\search\ProvidersSearch */
?>

<?php if (!empty($providers['models'])) : ?>
    <?php foreach ($providers['models'] as $provider) : ?>
        <div class="form-group">
            <label for="privder_api-1"><?= $provider['site'] ?> API</label>
            <input type="text" class="form-control" id="privder_api-1" value="<?= $provider['apikey'] ?>">
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No providers</p>
<?php endif; ?>
<hr>
<button class="btn btn-success m-btn--air">Save changes</button>