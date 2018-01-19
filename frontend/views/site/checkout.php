<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= Yii::t('app', 'checkout.redirect.title') ?></title>
</head>
<style>
    html,body{
        background: #f8f9fa;
        width: 100%;
        height: 100%;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        margin: 0px;
        padding: 0px;
        color: #212529;
    }
    .page-row{
        display: flex;
        flex-wrap: wrap;
        height: 100%;
        width: 100%;
        align-items: center;
        text-align: center;
    }
    .page-content{
        width: 100%;
        text-align: center;
    }
    .page-description{
        color: #868e96;
        font-size: 16px;
    }
    .page-description button{
        padding: 6px 6px;
        font-size: 16px;
        background: #868e96;
        text-decoration: none;
        color: #fff;
        display: inline-block;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }
    .page-description a:hover{
        background: #c3c3c3;
    }
    .page-description p{
        padding: 0px;
        margin: 0px;
    }
</style>
<body>
<div class="page-row">
    <div class="page-content">
        <div class="page-description">

            <form action="<?= $form['action'] ?>" method="<?= $form['method'] ?>" id="sendform"  accept-charset="<?= $form['charset'] ?>">
                <?php foreach ($data as $key => $value) : ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
                <?php endforeach; ?>
                <p><?= Yii::t('app', 'checkout.redirect.redirect') ?> <button type="submit"><?= Yii::t('app', 'checkout.redirect.go') ?></button></p>
            </form>

        </div>
    </div>
</div>
<script type="text/javascript">
    document.forms["sendform"].submit();
</script>
</body>
</html>
