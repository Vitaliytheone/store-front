<?php
    /* @var $this \yii\web\View */

$this->title = \Yii::t('admin', 'payments.page_title');
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
                'active' => 'general'
            ]) ?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= \Yii::t('admin', 'settings.section_general_title') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <form id="settings-general-form" action="/admin/settings" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="UTExLUJYc3UiQFRXNTE9IiVYWB0sEwsTFwFWfQc9JUNmSANhBjY5Jg==">

                    <div class="row">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <div class="form-group">
                                <div>
                                    <?= \Yii::t('admin', 'settings.section_general_logo_title') ?>
                                </div>
                                <label for="setting-logo">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= \Yii::t('admin', 'settings.section_general_logo_button_upload_title') ?>
                                    </a>
                                    <input id="setting-logo" type="file" class="settings-file">
                                </label>
                                <small class="form-text text-muted">
                                    <?= \Yii::t('admin', 'settings.section_general_logo_text_limits') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-1 order-lg-2">
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="#" class="sommerce-settings__delete-image" data-toggle="modal" data-target="#delete-modal"><span class="flaticon-cancel"></span></a>
                                <img src="http://fastinsta.sommerce.net/upload/logo/14954621475922f103a72fe3.74873262.png" alt="...">
                            </div>
                        </div>

                        <div class="col-lg-7 order-4 order-lg-4">
                            <div class="form-group">
                                <div>
                                    <?= \Yii::t('admin', 'settings.section_general_favicon_title') ?>
                                </div>
                                <label for="setting-favicon">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= \Yii::t('admin', 'settings.section_general_favicon_button_upload_title') ?>
                                    </a>
                                    <input id="setting-favicon" type="file" class="settings-file">
                                </label>
                                <small class="form-text text-muted">
                                    <?= \Yii::t('admin', 'settings.section_general_favicon_text_limits') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-3 order-lg-4">
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="#" class="sommerce-settings__delete-image" data-toggle="modal" data-target="#delete-modal"><span class="flaticon-cancel"></span></a>
                                <img src="http://d30fl32nd2baj9.cloudfront.net/media/2017/04/15/1492274418_google-plus.png/BINARY/1492274418_google-plus.png" alt="...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="settingsgeneralform-name">
                            <?= \Yii::t('admin', 'settings.section_general_store_name_title') ?>
                        </label>
                        <input type="text" id="settingsgeneralform-name" class="form-control" name="SettingsGeneralForm[name]" value="FastInstaFollowers" autofocus="" aria-required="true"
                               placeholder="<?= \Yii::t('admin', 'settings.section_general_store_input_placeholder') ?>">

                    </div>
                    <div class="form-group field-settingsgeneralform-timezone required">
                        <label class="control-label" for="settingsgeneralform-timezone">
                            <?= \Yii::t('admin', 'settings.section_general_timezone_title') ?>
                        </label>
                        <select id="settingsgeneralform-timezone" class="form-control" name="SettingsGeneralForm[timezone]" aria-required="true">
                            <option value="-12:00">(UTC -12:00) Baker/Howland Island</option>
                            <option value="-11:00">(UTC -11:00) Niue</option>
                            <option value="-10:00">(UTC -10:00) Hawaii-Aleutian Standard Time, Cook Islands, Tahiti</option>
                            <option value="-9:30">(UTC -9:30) Marquesas Islands</option>
                            <option value="-9:00">(UTC -9:00) Alaska Standard Time, Gambier Islands</option>
                            <option value="-8:00">(UTC -8:00) Pacific Standard Time, Clipperton Island</option>
                            <option value="-7:00">(UTC -7:00) Mountain Standard Time</option>
                            <option value="-6:00">(UTC -6:00) Central Standard Time</option>
                            <option value="-5:00">(UTC -5:00) Eastern Standard Time, Western Caribbean Standard Time</option>
                            <option value="-4:30">(UTC -4:30) Venezuelan Standard Time</option>
                            <option value="-4:00">(UTC -4:00) Atlantic Standard Time, Eastern Caribbean Standard Time</option>
                            <option value="-3:30">(UTC -3:30) Newfoundland Standard Time</option>
                            <option value="-3:00">(UTC -3:00) Argentina, Brazil, French Guiana, Uruguay</option>
                            <option value="-2:00">(UTC -2:00) South Georgia/South Sandwich Islands</option>
                            <option value="-1:00">(UTC -1:00) Azores, Cape Verde Islands</option>
                            <option value="UTC" selected="">(UTC) Greenwich Mean Time, Western European Time</option>
                            <option value="+1:00">(UTC +1:00) Central European Time, West Africa Time</option>
                            <option value="+2:00">(UTC +2:00) Central Africa Time, Eastern European Time, Kaliningrad Time</option>
                            <option value="+3:00">(UTC +3:00) Moscow Time, East Africa Time, Arabia Standard Time</option>
                            <option value="+3:30">(UTC +3:30) Iran Standard Time</option>
                            <option value="+4:00">(UTC +4:00) Azerbaijan Standard Time, Samara Time</option>
                            <option value="+4:30">(UTC +4:30) Afghanistan</option>
                            <option value="+5:00">(UTC +5:00) Pakistan Standard Time, Yekaterinburg Time</option>
                            <option value="+5:30">(UTC +5:30) Indian Standard Time, Sri Lanka Time</option>
                            <option value="+5:45">(UTC +5:45) Nepal Time</option>
                            <option value="+6:00">(UTC +6:00) Bangladesh Standard Time, Bhutan Time, Omsk Time</option>
                            <option value="+6:30">(UTC +6:30) Cocos Islands, Myanmar</option>
                            <option value="+7:00">(UTC +7:00) Krasnoyarsk Time, Cambodia, Laos, Thailand, Vietnam</option>
                            <option value="+8:00">(UTC +8:00) Australian Western Standard Time, Beijing Time, Irkutsk Time</option>
                            <option value="+8:45">(UTC +8:45) Australian Central Western Standard Time</option>
                            <option value="+9:00">(UTC +9:00) Japan Standard Time, Korea Standard Time, Yakutsk Time</option>
                            <option value="+9:30">(UTC +9:30) Australian Central Standard Time</option>
                            <option value="+10:00">(UTC +10:00) Australian Eastern Standard Time, Vladivostok Time</option>
                            <option value="+10:30">(UTC +10:30) Lord Howe Island</option>
                            <option value="+11:00">(UTC +11:00) Srednekolymsk Time, Solomon Islands, Vanuatu</option>
                            <option value="+11:30">(UTC +11:30) Norfolk Island</option>
                            <option value="+12:00">(UTC +12:00) Fiji, Gilbert Islands, Kamchatka Time, New Zealand Standard Time</option>
                            <option value="+12:45">(UTC +12:45) Chatham Islands Standard Time</option>
                            <option value="+13:00">(UTC +13:00) Samoa Time Zone, Phoenix Islands Time, Tonga</option>
                            <option value="+14:00">(UTC +14:00) Line Islands</option>
                        </select>

                    </div>

                    <div class="card card-white ">
                        <div class="card-body">

                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    <?= \Yii::t('admin', 'settings.section_general_seo_title') ?>
                                </div>
                                <div class="col-sm-4 text-sm-right">
                                    <button class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">
                                        <?= \Yii::t('admin', 'settings.section_general_seo_button_edit_title') ?>
                                    </button>
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="seo-preview__title edit-seo__title">
                                    <?= \Yii::t('admin', 'settings.section_general_seo_index_title') ?>
                                </div>
                                <div class="seo-preview__url">http://fastinsta.sommerce.net</div>
                                <div class="seo-preview__description edit-seo__meta">
                                    <?= \Yii::t('admin', 'settings.section_general_seo_meta_title_text_default') ?>
                                </div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title">
                                        <?= \Yii::t('admin', 'settings.section_general_seo_index_title') ?>
                                    </label>
                                    <input class="form-control" id="edit-seo__title"
                                           value="<?= \Yii::t('admin', 'settings.section_general_seo_index_title_input_default') ?>">
                                    <small class="form-text text-muted"><span class="edit-seo__title-muted"></span>
                                        <?= \Yii::t('admin', 'settings.section_general_seo_index_title_input_limits') ?>
                                         </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta">
                                        <?= \Yii::t('admin', 'settings.section_general_seo_meta_title') ?>
                                    </label>
                                    <textarea class="form-control" id="edit-seo__meta" rows="3">
                                        <?= \Yii::t('admin', 'settings.section_general_seo_meta_title_text_default') ?>
                                    </textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span>
                                        <?= \Yii::t('admin', 'settings.section_general_seo_meta_title_input_limits') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>


                <hr>
                <div class="form-group">
                    <button type="submit" class="btn btn-success m-btn--air" name="save-button">
                        <?= \Yii::t('admin', 'settings.button_save_title') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end::Body -->