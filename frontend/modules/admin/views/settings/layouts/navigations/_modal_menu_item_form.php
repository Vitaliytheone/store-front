<?php

/* @var $linkTypes array */

?>

<div class="modal fade edit_navigation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add menu item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="navForm" method="post" enctype="multipart/form-data" name="NavForm">

                <div class="modal-body">

                    <div class="form-error"></div>

                    <div class="form-group">
                        <label for="menu_item_name">Name</label>
                        <input type="text" class="form-control form_field__name" id="menu_item_name" placeholder="Name"
                               name="NavForm[name]" value="">
                    </div>

                    <div class="form-group">
                        <label for="select-menu-link">Link</label>
                        <select class="form-control form_field__link" id="select-menu-link" name="NavForm[link]">
                            <?php foreach ($linkTypes as $linkType => $linkName): ?>
                                <option value="<?= $linkType ?>"><?= $linkName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group hide-link link-23">
                        <label for="link_id">Page/Product</label>
                        <select class="form-control form_field__link_id" id="link_id" name="NavForm[link_id]">
                            <option value="link_id">Link Name</option>
                        </select>
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

