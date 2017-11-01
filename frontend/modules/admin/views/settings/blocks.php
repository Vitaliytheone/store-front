<?php
    /* @var $this \yii\web\View */

    use frontend\modules\admin\components\Url;
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
                'active' => 'blocks'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Blocks
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <div class="sommerce-card__block m-portlet">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="card-block__title">
                                Slider
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <div class="card-block__switch">
                                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" checked name="">
																			<span></span>
																		</label>
																	</span>
                                </div>
                                <div class="card-block__actions">
                                    <a href="<?= Url::toRoute('/settings/edit-block') ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sommerce-card__block m-portlet">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="card-block__title">
                                Features
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <div class="card-block__switch">
                                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" checked name="">
																			<span></span>
																		</label>
																	</span>
                                </div>
                                <div class="card-block__actions">
                                    <a href="<?= Url::toRoute('/settings/edit-block') ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sommerce-card__block m-portlet">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="card-block__title">
                                Review
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <div class="card-block__switch">
                                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" name="">
																			<span></span>
																		</label>
																	</span>
                                </div>
                                <div class="card-block__actions">
                                    <a href="<?= Url::toRoute('/settings/edit-block') ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sommerce-card__block m-portlet">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="card-block__title">
                                Steps
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <div class="card-block__switch">
                                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" name="">
																			<span></span>
																		</label>
																	</span>
                                </div>
                                <div class="card-block__actions">
                                    <a href="<?= Url::toRoute('/settings/edit-block') ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- end::Body -->