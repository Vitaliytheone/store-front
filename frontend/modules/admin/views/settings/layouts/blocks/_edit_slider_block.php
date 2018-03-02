<div class="container mt-2">

    <!--Feature setting panel START-->
    <div class="row editor-block border border-grey align-items-center m-portlet m-portlet--mobile">
        <div class="col-md-4">
            <button class="btn btn-primary cursor-pointer new-preview">New slide</button>
        </div>
        <div class="col-md-8 text-md-right">




            <div class="editor-block__setting-block flex-wrap ">
                <div class="editor-block__setting-title">Effect</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" class="slider-effects" name="feature-column" value="fade" autocomplete="off"> Fade
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" class="slider-effects" name="feature-column" value="slide" autocomplete="off"> Slide
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="editor-block__setting-block flex-wrap">
                <div class="editor-block__setting-title">Rotation interval</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="5000" autocomplete="off">  5
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="7000" autocomplete="off"> 7
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="10000" autocomplete="off"> 10
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="15000" autocomplete="off"> 15
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">
        <!-- Swiper -->
        <div class="swiper-container block-slider">
            <div class="swiper-wrapper">

            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>


<!--Are your sure-->
<div class="modal fade" id="delete-feature-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p>Are your sure that your want to delete this feature?</p>
                        <button class="btn btn-secondary cursor-pointer" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="feature-delete" data-dismiss="modal">Yes, delete it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <option value="web">Web adress</option>
                    </select>
                </div>


                <div class="form-group hide-link slider-link__type slider-link__type-products">
                    <label for="exampleFormControlSelect1">Products</label>
                    <select class="form-control link-input__products" id="exampleFormControlSelect1">
                        <option value="/products/1">Facebook</option>
                        <option value="/products/2">Twitter</option>
                        <option value="/products/3">Instagram</option>
                    </select>
                </div>

                <div class="form-group hide-link slider-link__type  slider-link__type-web">
                    <label for="menu_item_name">Web adress</label>
                    <input type="text" class="form-control link-input__web" id="menu_item_name" placeholder="/">
                </div>

                <div class="form-group hide-link slider-link__type slider-link__type-page">
                    <label for="exampleFormControlSelect2">Page</label>
                    <select class="form-control link-input__page" id="exampleFormControlSelect2">
                        <option value="contact-us">Contact US</option>
                        <option value="terms">Terms</option>
                        <option value="page-1">Page 1</option>
                        <option value="page-2">Page 2</option>
                        <option value="page-3">Page 3</option>
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