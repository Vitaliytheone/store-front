<?php

use \yii\helpers\ArrayHelper;

/* @var $linkTypes array */

?>

<div class="modal fade edit_navigation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-loader hidden"></div>

            <div class="modal-header">
                <h5 class="modal-title">Add menu item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="navForm" method="post" enctype="multipart/form-data" name="NavForm" data-links_url="">

                <div class="modal-body">

                    <div class="form-error alert-danger alert d-none"></div>

                    <div class="form-group">
                        <label for="menu_item_name">Name</label>
                        <input type="text" class="form-control form_field__name" id="menu_item_name" placeholder="Name"
                               name="NavForm[name]" value="">
                    </div>

                    <div class="form-group">
                        <label for="select-menu-link">Link</label>
                        <select class="form-control form_field__link" id="select-menu-link" name="NavForm[link]">

                            <?php foreach ($linkTypes as $linkType => $link): ?>
                                <option value="<?= $linkType ?>" <?php if (array_key_exists('select_id', $link)): ?> data-select_id="<?= $link['select_id'] ?>" <?php endif; ?> <?php if (in_array('fetched', $link)): ?> data-fetched="1" <?php endif; ?>>
                                    <?= $link['name'] ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="form-group hide-link link-23">
                        <label for="link_id">Page/Product</label>
                        <select class="form-control form_field__link_id" id="link_id" name="NavForm[link_id]"></select>
                    </div>

                    <div class="form-group hide-link link-4">
                        <label for="web_address">Web address</label>
                        <input type="text" class="form-control form_field__url" id="web_address" placeholder="/" name="NavForm[url]">
                    </div>

                </div>

                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary m-btn--air">Add menu item</button>
                    <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>

