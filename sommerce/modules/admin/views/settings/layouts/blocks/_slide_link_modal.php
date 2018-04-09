<?php
    /* @var $this \yii\web\View */

    use sommerce\modules\admin\models\search\LinksSearch;

    $searchModel = new LinksSearch();
?>
<!-- Modal -->
<div class="modal fade" id="learn-more" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Slide link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="menu_item_name">Button name</label>
                    <input type="text" class="form-control learn-more__input" value="Learn more">
                </div>

                <div class="form-group">
                    <label for="select-menu__link">Link</label>
                    <select class="form-control" id="select-menu__link">
                        <option value="none">None</option>
                        <option value="home">Home page</option>
                        <option value="products">Products</option>
                        <option value="page">Page</option>
                        <option value="web">Web address</option>
                    </select>
                </div>


                <div class="form-group hide-link slider-link__type slider-link__type-products">
                    <label for="exampleFormControlSelect1">Products</label>
                    <select class="form-control link-input__products" id="exampleFormControlSelect1">
                        <?php foreach ($searchModel->searchProductsLinks() as $products) : ?>
                            <option value="/<?= $products['url'] ?>"><?= $products['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group hide-link slider-link__type  slider-link__type-web">
                    <label for="menu_item_name">Web adress</label>
                    <input type="text" class="form-control link-input__web" id="menu_item_name" placeholder="/">
                </div>

                <div class="form-group hide-link slider-link__type slider-link__type-page">
                    <label for="exampleFormControlSelect2">Page</label>
                    <select class="form-control link-input__page" id="exampleFormControlSelect2">
                        <?php foreach ($searchModel->searchPagesLinks() as $page) : ?>
                            <option value="/<?= $page['url'] ?>"><?= $page['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="learn-more__save" data-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>