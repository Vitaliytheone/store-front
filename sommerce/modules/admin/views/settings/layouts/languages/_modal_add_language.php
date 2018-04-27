<?php
    /** @var $availableLanguages array */
    /** @var  $model */
?>

<div class="modal fade add-language-modal" data-backdrop="static" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.languages_modal_title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-add-language">
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label for="lang-selector"><?= Yii::t('admin', 'settings.languages_modal_language') ?></label>
                        <select id="lang-selector" class="form-control m-input form_field__languages_list" name="lang_code">
                            <option selected="selected" disabled="disabled" value=""><?= Yii::t('admin', 'settings.languages_modal_select_item') ?></option>
                            <?php foreach ($availableLanguages as $code => $name): ?>
                                <option value="<?= $code ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary m-btn--air btn_submit"><?= Yii::t('admin', 'settings.languages_modal_save') ?></button>
                    <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.languages_modal_cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>