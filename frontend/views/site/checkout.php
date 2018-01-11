<div class="container section">
    <div class="row">
        <div class="col-md-12">

            <h1 class="page-title"><?= Yii::t('app', 'checkout.title') ?></h1>

            <form action="<?= $form['action'] ?>" method="<?= $form['method'] ?>" id="sendform"  accept-charset="<?= $form['charset'] ?>">
                <?php foreach ($data as $key => $value) : ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
                <?php endforeach; ?>
                <p><?= Yii::t('app', 'checkout.redirect') ?> <button type="submit"><?= Yii::t('app', 'checkout.go') ?></button></p>
            </form>

        </div>
    </div>
</div>
<script type="text/javascript">
    document.forms["sendform"].submit();
</script>