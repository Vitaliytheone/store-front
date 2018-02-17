<?php
    /* @var $this \yii\web\View */
?>
<div class="container mt-2">

    <!--Feature setting panel START-->
    <div class="row editor-block border border-grey align-items-center m-portlet m-portlet--mobile">
        <div class="col-md-4">
            <button class="btn btn-primary cursor-pointer" id="feature-new">New feature</button>
        </div>
        <div class="col-md-8 text-md-right">


            <div class="editor-block__setting-block flex-wrap min-width-200">
                <div class="editor-block__setting-title">Icon size
                    <div class="pull-right"><span class="feature-icon-size-show">10</span>px</div>
                </div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div id="feature-fontSize"></div>
                </div>
            </div>


            <div class="editor-block__setting-block flex-wrap">
                <div class="editor-block__setting-title">Column</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-column" value="6" autocomplete="off" class="feature-column"> 2
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-column" value="4" autocomplete="off" class="feature-column"> 3
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-column" value="3" autocomplete="off" class="feature-column"> 4
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="editor-block__setting-block flex-wrap">
                <div class="editor-block__setting-title">Align feature</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" value="left" autocomplete="off" class="feature-align"> <span class="fa fa-align-left"></span> left
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" value="center" autocomplete="off" class="feature-align"> <span class="fa fa-align-center"></span> center
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div>


    </div>
    <!--Feature edit Title block & Description START -->
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">
        <div class="col-12">
            <div class="editor-block__title-h1">
                <div class="editor-textarea__text-edit-off">
                    <textarea class="editor-textarea__h editor-textarea__h2 js-auto-size feature-title" data-textarea-title="header-title" rows="1" spellcheck="false" placeholder="Add title..."></textarea>
                    <div class="editor-textarea__text-edit-action">
                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>
                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>
                    </div>
                </div>
            </div>
            <div class="editor-block__description">
                <div class="editor_textarea-block">
                    <div class="editor-textarea__text-edit-off">
                        <textarea class="editor_textarea__text js-auto-size text-center feature-description" data-textarea-title="header-description" rows="1" spellcheck="false" placeholder="Add text..."></textarea>
                        <div class="editor-textarea__text-edit-action">
                            <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>
                            <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Feature edit cards START -->
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">

        <ul class="row" id="feature-list"></ul>

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
<!-- Edit icon -->
<div class="modal fade" id="preview-edit-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit feature icon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-3 editor-modal__block-height">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                            <a class="nav-link active" id="application-tab" data-toggle="pill" href="#application" role="tab" aria-controls="v-pills-home" aria-expanded="true">Popular</a>
                            <a class="nav-link" id="accessibility-tab" data-toggle="pill" href="#accessibility" role="tab" aria-controls="v-pills-profile" aria-expanded="true">Accessibility</a>
                            <a class="nav-link" id="spinner-tab" data-toggle="pill" href="#spinner" role="tab" aria-controls="v-pills-messages" aria-expanded="true">Spinner</a>
                            <a class="nav-link" id="hand-tab" data-toggle="pill" href="#hand" role="tab" aria-controls="v-pills-messages" aria-expanded="true">Hand</a>
                            <a class="nav-link" id="transportation-tab" data-toggle="pill" href="#transportation" role="tab" aria-controls="v-pills-messages" aria-expanded="true">Transportation</a>
                            <a class="nav-link" id="payment-tab" data-toggle="pill" href="#payment" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Payment</a>
                            <a class="nav-link" id="gender-tab" data-toggle="pill" href="#gender" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Gender</a>
                            <a class="nav-link" id="files-tab" data-toggle="pill" href="#files" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Files</a>
                            <a class="nav-link" id="form-tab" data-toggle="pill" href="#form" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Form</a>
                            <a class="nav-link" id="charts-tab" data-toggle="pill" href="#charts" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Charts</a>
                            <a class="nav-link" id="currency-tab" data-toggle="pill" href="#currency" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Currency</a>
                            <a class="nav-link" id="textEditor-tab" data-toggle="pill" href="#textEditor" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Text editor</a>
                            <a class="nav-link" id="directional-tab" data-toggle="pill" href="#directional" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Directional</a>
                            <a class="nav-link" id="video-tab" data-toggle="pill" href="#video" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Video</a>
                            <a class="nav-link" id="brands-tab" data-toggle="pill" href="#brands" role="tab" aria-controls="v-pills-settings" aria-expanded="true">Brands</a>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom active-icon" data-icon-name="fa-picture-o">
                                            <span class="fa fa-picture-o"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="accessibility" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-american-sign-language-interpreting">
                                            <span class="fa fa-american-sign-language-interpreting"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-american-sign-language-interpreting">
                                            <span class="fa fa-american-sign-language-interpreting"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-assistive-listening-systems">
                                            <span class="fa fa-assistive-listening-systems"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-audio-description">
                                            <span class="fa fa-audio-description"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-blind">
                                            <span class="fa fa-blind"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-braille">
                                            <span class="fa fa-braille"></span>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc">
                                            <span class="fa fa-cc"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-deaf">
                                            <span class="fa fa-deaf"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-deafness">
                                            <span class="fa fa-deafness"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-question-circle-o">
                                            <span class="fa fa-question-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-sign-language">
                                            <span class="fa fa-sign-language"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-tty">
                                            <span class="fa fa-tty"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-universal-access">
                                            <span class="fa fa-universal-access"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-wheelchair">
                                            <span class="fa fa-wheelchair"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-wheelchair-alt">
                                            <span class="fa fa-wheelchair-alt"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="spinner" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-circle-o-notch">
                                            <span class="fa fa-circle-o-notch"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cog">
                                            <span class="fa fa-cog"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-gear">
                                            <span class="fa fa-gear"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-refresh">
                                            <span class="fa fa-refresh"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-spinner">
                                            <span class="fa fa-spinner"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="hand" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-grab-o">
                                            <span class="fa fa-hand-grab-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-lizard-o">
                                            <span class="fa fa-hand-lizard-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-o-down">
                                            <span class="fa fa-hand-o-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-o-left">
                                            <span class="fa fa-hand-o-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-o-right">
                                            <span class="fa fa-hand-o-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-o-up">
                                            <span class="fa fa-hand-o-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-paper-o">
                                            <span class="fa fa-hand-paper-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-peace-o">
                                            <span class="fa fa-hand-peace-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-pointer-o">
                                            <span class="fa fa-hand-pointer-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-rock-o">
                                            <span class="fa fa-hand-rock-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-scissors-o">
                                            <span class="fa fa-hand-scissors-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-spock-o">
                                            <span class="fa fa-hand-spock-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-hand-stop-o">
                                            <span class="fa fa-hand-stop-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-thumbs-down">
                                            <span class="fa fa-thumbs-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-thumbs-o-down">
                                            <span class="fa fa-thumbs-o-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-thumbs-o-up">
                                            <span class="fa fa-thumbs-o-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-thumbs-up">
                                            <span class="fa fa-thumbs-up"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="transportation" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-ambulance">
                                            <span class="fa fa-ambulance"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-automobile">
                                            <span class="fa fa-automobile"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bicycle">
                                            <span class="fa fa-bicycle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bus">
                                            <span class="fa fa-bus"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cab">
                                            <span class="fa fa-cab"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-car">
                                            <span class="fa fa-car"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-fighter-jet">
                                            <span class="fa fa-fighter-jet"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-motorcycle">
                                            <span class="fa fa-motorcycle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-plane">
                                            <span class="fa fa-plane"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rocket">
                                            <span class="fa fa-rocket"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-ship">
                                            <span class="fa fa-ship"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-space-shuttle">
                                            <span class="fa fa-space-shuttle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-subway">
                                            <span class="fa fa-subway"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-taxi">
                                            <span class="fa fa-taxi"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-train">
                                            <span class="fa fa-train"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-truck">
                                            <span class="fa fa-truck"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-amex">
                                            <span class="fa fa-cc-amex"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-diners-club">
                                            <span class="fa fa-cc-diners-club"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-discover">
                                            <span class="fa fa-cc-discover"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-jcb">
                                            <span class="fa fa-cc-jcb"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-mastercard">
                                            <span class="fa fa-cc-mastercard"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-paypal">
                                            <span class="fa fa-cc-paypal"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-stripe">
                                            <span class="fa fa-cc-stripe"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cc-visa">
                                            <span class="fa fa-cc-visa"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-credit-card">
                                            <span class="fa fa-credit-card"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-credit-card-alt">
                                            <span class="fa fa-credit-card-alt"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-paypal">
                                            <span class="fa fa-paypal"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-google-wallet">
                                            <span class="fa fa-google-wallet"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="gender" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-genderless">
                                            <span class="fa fa-genderless"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-intersex">
                                            <span class="fa fa-intersex"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mars">
                                            <span class="fa fa-mars"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mars-stroke">
                                            <span class="fa fa-mars-stroke"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mars-double">
                                            <span class="fa fa-mars-double"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mars-stroke-h">
                                            <span class="fa fa-mars-stroke-h"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mars-stroke-v">
                                            <span class="fa fa-mars-stroke-v"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-mercury">
                                            <span class="fa fa-mercury"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-neuter">
                                            <span class="fa fa-neuter"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-transgender">
                                            <span class="fa fa-transgender"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-transgender-alt">
                                            <span class="fa fa-transgender-alt"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-venus">
                                            <span class="fa fa-venus"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-venus-double">
                                            <span class="fa fa-venus-double"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-venus-mars">
                                            <span class="fa fa-venus-mars"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file">
                                            <span class="fa fa-file"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-archive-o">
                                            <span class="fa fa-file-archive-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-audio-o">
                                            <span class="fa fa-file-audio-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-code-o">
                                            <span class="fa fa-file-code-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-excel-o">
                                            <span class="fa fa-file-excel-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-image-o">
                                            <span class="fa fa-file-image-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-movie-o">
                                            <span class="fa fa-file-movie-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-o">
                                            <span class="fa fa-file-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-pdf-o">
                                            <span class="fa fa-file-pdf-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-photo-o">
                                            <span class="fa fa-file-photo-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-picture-o">
                                            <span class="fa fa-file-picture-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-powerpoint-o">
                                            <span class="fa fa-file-powerpoint-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-sound-o">
                                            <span class="fa fa-file-sound-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-text">
                                            <span class="fa fa-file-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-text-o">
                                            <span class="fa fa-file-text-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-video-o">
                                            <span class="fa fa-file-video-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-word-o">
                                            <span class="fa fa-file-word-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-file-zip-o">
                                            <span class="fa fa-file-zip-o"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="form" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-check-square">
                                            <span class="fa fa-check-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-check-square-o">
                                            <span class="fa fa-check-square-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-circle">
                                            <span class="fa fa-circle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-circle-o">
                                            <span class="fa fa-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-dot-circle-o">
                                            <span class="fa fa-dot-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-minus-square">
                                            <span class="fa fa-minus-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-plus-square">
                                            <span class="fa fa-plus-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-plus-square-o">
                                            <span class="fa fa-plus-square-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-square">
                                            <span class="fa fa-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-square-o">
                                            <span class="fa fa-square-o"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="charts" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-area-chart">
                                            <span class="fa fa-area-chart"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bar-chart">
                                            <span class="fa fa-bar-chart"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bar-chart-o">
                                            <span class="fa fa-bar-chart-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-line-chart">
                                            <span class="fa fa-line-chart"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-pie-chart">
                                            <span class="fa fa-pie-chart"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="currency" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bitcoin">
                                            <span class="fa fa-bitcoin"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-btc">
                                            <span class="fa fa-btc"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cny">
                                            <span class="fa fa-cny"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-dollar">
                                            <span class="fa fa-dollar"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-eur">
                                            <span class="fa fa-eur"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-euro">
                                            <span class="fa fa-euro"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-gbp">
                                            <span class="fa fa-gbp"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-gg">
                                            <span class="fa fa-gg"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-gg-circle">
                                            <span class="fa fa-gg-circle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-ils">
                                            <span class="fa fa-ils"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-inr">
                                            <span class="fa fa-inr"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-jpy">
                                            <span class="fa fa-jpy"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rkw">
                                            <span class="fa fa-krw"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-money">
                                            <span class="fa fa-money"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rmb">
                                            <span class="fa fa-rmb"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rouble">
                                            <span class="fa fa-rouble"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rub">
                                            <span class="fa fa-rub"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-ruble">
                                            <span class="fa fa-ruble"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rupee">
                                            <span class="fa fa-rupee"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-shekel">
                                            <span class="fa fa-shekel"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-sheqel">
                                            <span class="fa fa-sheqel"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-try">
                                            <span class="fa fa-try"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-turkish-lira">
                                            <span class="fa fa-turkish-lira"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-usd">
                                            <span class="fa fa-usd"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-won">
                                            <span class="fa fa-won"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-yen">
                                            <span class="fa fa-yen"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="textEditor" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-align-center">
                                            <span class="fa fa-align-center"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-align-justify">
                                            <span class="fa fa-align-justify"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-align-left">
                                            <span class="fa fa-align-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-align-right">
                                            <span class="fa fa-align-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bold">
                                            <span class="fa fa-bold"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chain">
                                            <span class="fa fa-chain"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chain-broken">
                                            <span class="fa fa-chain-broken"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-clipboard">
                                            <span class="fa fa-clipboard"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-columns">
                                            <span class="fa fa-columns"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-copy">
                                            <span class="fa fa-copy"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-cut">
                                            <span class="fa fa-cut"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-outdent">
                                            <span class="fa fa-outdent"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-eraser">
                                            <span class="fa fa-eraser"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-font">
                                            <span class="fa fa-font"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-header">
                                            <span class="fa fa-header"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-list-alt">
                                            <span class="fa fa-list-alt"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-list">
                                            <span class="fa fa-list"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-link">
                                            <span class="fa fa-link"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-turkish-lira">
                                            <span class="fa fa-turkish-lira"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-indent">
                                            <span class="fa fa-indent"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-italic">
                                            <span class="fa fa-italic"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-list-ol">
                                            <span class="fa fa-list-ol"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-list-ul">
                                            <span class="fa fa-list-ul"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-outdent">
                                            <span class="fa fa-outdent"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-paperclip">
                                            <span class="fa fa-paperclip"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-paragraph">
                                            <span class="fa fa-paragraph"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-paste">
                                            <span class="fa fa-paste"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-repeat">
                                            <span class="fa fa-repeat"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rotate-left">
                                            <span class="fa fa-rotate-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-rotate-right">
                                            <span class="fa fa-rotate-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-save">
                                            <span class="fa fa-save"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-scissors">
                                            <span class="fa fa-scissors"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-strikethrough">
                                            <span class="fa fa-strikethrough"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-subscript">
                                            <span class="fa fa-subscript"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-superscript">
                                            <span class="fa fa-superscript"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-table">
                                            <span class="fa fa-table"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-text-height">
                                            <span class="fa fa-text-height"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-text-width">
                                            <span class="fa fa-text-width"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-text-th">
                                            <span class="fa fa-th"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-th-large">
                                            <span class="fa fa-th-large"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-th-list">
                                            <span class="fa fa-th-list"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-underline">
                                            <span class="fa fa-underline"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-undo">
                                            <span class="fa fa-undo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="directional" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-double-up">
                                            <span class="fa fa-angle-double-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-double-down">
                                            <span class="fa fa-angle-double-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-double-left">
                                            <span class="fa fa-angle-double-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-double-right">
                                            <span class="fa fa-angle-double-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-down">
                                            <span class="fa fa-angle-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-left">
                                            <span class="fa fa-angle-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-right">
                                            <span class="fa fa-angle-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angle-up">
                                            <span class="fa fa-angle-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-down">
                                            <span class="fa fa-arrow-circle-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-left">
                                            <span class="fa fa-arrow-circle-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-right">
                                            <span class="fa fa-arrow-circle-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-up">
                                            <span class="fa fa-arrow-circle-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-o-down">
                                            <span class="fa fa-arrow-circle-o-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-o-up">
                                            <span class="fa fa-arrow-circle-o-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-o-left">
                                            <span class="fa fa-arrow-circle-o-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-circle-o-right">
                                            <span class="fa fa-arrow-circle-o-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-down">
                                            <span class="fa fa-arrow-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-left">
                                            <span class="fa fa-arrow-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-right">
                                            <span class="fa fa-arrow-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrow-up">
                                            <span class="fa fa-arrow-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrows">
                                            <span class="fa fa-arrows"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-down">
                                            <span class="fa fa-caret-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-left">
                                            <span class="fa fa-caret-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-right">
                                            <span class="fa fa-caret-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-up">
                                            <span class="fa fa-caret-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-down">
                                            <span class="fa fa-caret-square-o-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-up">
                                            <span class="fa fa-caret-square-o-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-left">
                                            <span class="fa fa-caret-square-o-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-circle-down">
                                            <span class="fa fa-chevron-circle-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-circle-up">
                                            <span class="fa fa-chevron-circle-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-circle-left">
                                            <span class="fa fa-chevron-circle-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-circle-right">
                                            <span class="fa fa-chevron-circle-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-down">
                                            <span class="fa fa-caret-square-o-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-left">
                                            <span class="fa fa-caret-square-o-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-right">
                                            <span class="fa fa-caret-square-o-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-caret-square-o-up">
                                            <span class="fa fa-caret-square-o-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-left">
                                            <span class="fa fa-chevron-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-right">
                                            <span class="fa fa-chevron-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-down">
                                            <span class="fa fa-chevron-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chevron-up">
                                            <span class="fa fa-chevron-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-exchange">
                                            <span class="fa fa-exchange"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-long-arrow-down">
                                            <span class="fa fa-long-arrow-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-long-arrow-left">
                                            <span class="fa fa-long-arrow-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-long-arrow-right">
                                            <span class="fa fa-long-arrow-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-long-arrow-up">
                                            <span class="fa fa-long-arrow-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-toggle-up">
                                            <span class="fa fa-toggle-up"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-toggle-left">
                                            <span class="fa fa-toggle-left"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-toggle-down">
                                            <span class="fa fa-toggle-down"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-toggle-right">
                                            <span class="fa fa-toggle-right"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-arrows-alt">
                                            <span class="fa fa-arrows-alt"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-backward">
                                            <span class="fa fa-backward"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-compress">
                                            <span class="fa fa-compress"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-eject">
                                            <span class="fa fa-eject"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-expand">
                                            <span class="fa fa-expand"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-fast-backward">
                                            <span class="fa fa-fast-backward"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-fast-forward">
                                            <span class="fa fa-fast-forward"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-forward">
                                            <span class="fa fa-forward"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-pause">
                                            <span class="fa fa-pause"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-pause-circle">
                                            <span class="fa fa-pause-circle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-pause-circle-o">
                                            <span class="fa fa-pause-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-play">
                                            <span class="fa fa-play"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-play-circle">
                                            <span class="fa fa-play-circle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-play-circle-o">
                                            <span class="fa fa-play-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-random">
                                            <span class="fa fa-random"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-step-backward">
                                            <span class="fa fa-step-backward"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-stop">
                                            <span class="fa fa-stop"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-stop-circle">
                                            <span class="fa fa-stop-circle"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-stop-circle-o">
                                            <span class="fa fa-stop-circle-o"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-youtube-play">
                                            <span class="fa fa-youtube-play"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="brands" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <div class="row editor-modal__block-height">
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-500px">
                                            <span class="fa fa-500px"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-adn">
                                            <span class="fa fa-adn"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-amazon">
                                            <span class="fa fa-amazon"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-android">
                                            <span class="fa fa-android"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-angellist">
                                            <span class="fa fa-angellist"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-apple">
                                            <span class="fa fa-apple"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-behance">
                                            <span class="fa fa-behance"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-behance-square">
                                            <span class="fa fa-behance-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bitbucket">
                                            <span class="fa fa-bitbucket"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bitbucket-square">
                                            <span class="fa fa-bitbucket-square"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-black-tie">
                                            <span class="fa fa-black-tie"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bluetooth">
                                            <span class="fa fa-bluetooth"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-bluetooth-b">
                                            <span class="fa fa-bluetooth-b"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-buysellads">
                                            <span class="fa fa-buysellads"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-chrome">
                                            <span class="fa fa-chrome"></span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="editor-modal__icons-block edit-modal__icons-action border border-light margin-bottom" data-icon-name="fa-codepen">
                                            <span class="fa fa-codepen"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor-pointer" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary cursor-pointer edit-modal__icons-save" data-dismiss="modal" id="feature-saveIcon">Save icon</button>
            </div>
        </div>
    </div>
</div>