<?php
    /* @var $this \yii\web\View */
?>

<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'navigations'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Navigation
                        </h3>
                    </div>

                    <div>
                        <div class="m-dropdown--align-right">
                            <button class="btn btn-primary  m-btn--air btn-brand cursor-pointer" data-toggle="modal" data-target=".add_navigation" data-backdrop="static">Add menu item</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <div class="dd" id="nestable">
                    <ol class="dd-list">
                        <li class="dd-item" data-id="3">
                            <div class="dd-handle">Home</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                        </li>
                        <li class="dd-item" data-id="1">
                            <div class="dd-handle">YouTube</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                            <ol class="dd-list">
                                <li class="dd-item" data-id="3">
                                    <div class="dd-handle">Buy YouTube Views</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy YouTube Likes</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy YouTube Subscribers</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            </ol>
                        </li>
                        <li class="dd-item" data-id="2">
                            <div class="dd-handle">Google plus</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                            <ol class="dd-list">
                                <li class="dd-item" data-id="3">
                                    <div class="dd-handle">Buy Google Plus Followers</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy Google Plus Likes</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            </ol>
                        </li>
                        <li class="dd-item" data-id="2">
                            <div class="dd-handle">SoundCloud</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                            <ol class="dd-list">
                                <li class="dd-item" data-id="3">
                                    <div class="dd-handle">Buy SoundCloud Followers</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy SoundCloud Likes</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy SoundCloud Plays</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                                <li class="dd-item" data-id="4">
                                    <div class="dd-handle">Buy SoundCloud Downloads</div>
                                    <div class="dd-edit-button">
                                        <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                            Edit
                                        </a>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                            <i class="la la-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            </ol>
                        </li>
                        <li class="dd-item" data-id="3">
                            <div class="dd-handle">Contact Us</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end::Body -->


<div class="modal fade add_navigation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add menu item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="menu_item_name">Name</label>
                        <input type="text" class="form-control" id="menu_item_name" placeholder="Name" value="Followers titter">
                    </div>
                    <div class="form-group">
                        <label for="select-menu-link">Link</label>
                        <select class="form-control" id="select-menu-link">
                            <option value="4">Home page</option>
                            <option value="1">Services</option>
                            <option value="2">Page</option>
                            <option value="3">Web adress</option>
                        </select>
                    </div>


                    <div class="form-group hide-link link-1">
                        <label for="exampleFormControlSelect1">Page</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Contact US</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                        </select>
                    </div>

                    <div class="form-group hide-link link-3">
                        <label for="menu_item_name">Web adress</label>
                        <input type="text" class="form-control" id="menu_item_name" placeholder="/">
                    </div>

                    <div class="form-group hide-link link-2">
                        <label for="exampleFormControlSelect2">Page</label>
                        <select class="form-control" id="exampleFormControlSelect2">
                            <option>Contact US</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                        </select>
                    </div>


                </form>

            </div>
            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-primary m-btn--air">Add menu item</button>
                <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade edit_navigation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit menu item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="menu_item_name">Name</label>
                        <input type="text" class="form-control" id="menu_item_name" placeholder="Name" value="Followers titter">
                    </div>
                    <div class="form-group">
                        <label for="select-menu-link">Link</label>
                        <select class="form-control" id="select-menu-link">
                            <option value="1">Services</option>
                            <option value="2">Page</option>
                            <option value="3">Web adress</option>
                        </select>
                    </div>


                    <div class="form-group hide-link link-1">
                        <label for="exampleFormControlSelect1">Page</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Contact US</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                        </select>
                    </div>

                    <div class="form-group hide-link link-3">
                        <label for="menu_item_name">Web adress</label>
                        <input type="text" class="form-control" id="menu_item_name" placeholder="/">
                    </div>

                    <div class="form-group hide-link link-2">
                        <label for="exampleFormControlSelect2">Page</label>
                        <select class="form-control" id="exampleFormControlSelect2">
                            <option>Contact US</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                            <option>Page name</option>
                        </select>
                    </div>


                </form>

            </div>
            <div class="modal-footer justify-content-start">

                <button type="button" class="btn btn-primary m-btn--air">Save changes</button>
                <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal">Cancel</button>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p>Are your sure that your want to delete this logo?</p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="feature-delete m-btn--air" data-dismiss="modal">Yes, delete it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>