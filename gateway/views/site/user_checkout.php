<div class="page-row">
    <div class="page-content">
        <div class="page-description">

            <form action="<?= $form['action'] ?>" method="<?= $form['method'] ?>" id="checkout"  accept-charset="<?= $form['charset'] ?>">
                <?php foreach ($data as $key => $value) : ?>
                    <input type="hidden" id="<?= $key ?>" name="<?= $key ?>" value="<?= $value ?>">
                <?php endforeach; ?>
            </form>

        </div>
    </div>
</div>