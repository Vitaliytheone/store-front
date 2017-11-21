<div class="container section">
    <div class="row">
        <div class="col-md-12">

            <h1 class="page-title">Checkout</h1>

            <form action="<?= $form['action'] ?>" method="<?= $form['method'] ?>" id="sendform"  accept-charset="<?= $form['charset'] ?>">
                <?php foreach ($data as $key => $value) : ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
                <?php endforeach; ?>
                <p>Redirecting... <button type="submit">Go</button></p>
            </form>

        </div>
    </div>
</div>
<script type="text/javascript">
    document.forms["sendform"].submit();
</script>