/**
 * /admin/settings custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminGeneral = {
    run: function (params) {

        /******************************************************************
         *            General settings SEO part interaction
         *******************************************************************/
        if ($('.edit-seo__title').length > 0) {
            (function () {

                var $storeName = $('#store-name'),
                    $seoTitle = $('#edit-seo__title');

                var seoTitleOnInit = $seoTitle.val(),
                    seoTitleTouched = false;

                var seoEdit = ['edit-seo__title', 'edit-seo__meta'];
                var _loop = function _loop(i) {
                    $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);

                    $("#" + seoEdit[i]).on('input', function (e) {
                        $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                        $('.' + seoEdit[i]).text($(e.target).val());
                    }).trigger('input');

                };
                for (var i = 0; i < seoEdit.length; i++) {
                    _loop(i);
                }

                $seoTitle.on('focus', function (e){
                   seoTitleTouched = true;
                });

                $storeName.on('input', function(e){
                    if (seoTitleOnInit !== '' || seoTitleTouched) {
                        return;
                    }
                    $seoTitle.val($(this).val()).trigger('input');
                });

            })();
        }

        /******************************************************************
         *            General settings delete logo & favicon
         ******************************************************************/
        var $modal = $('#delete-modal'),
        $deleteBtn = $modal.find('#delete-image');

        $modal.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget),
            actionUrl = button.attr('href');
            $deleteBtn.attr('href', actionUrl);
        });

        $modal.on('hidden.bs.modal', function (){
            $deleteBtn.attr('href', null);
        });


        /******************************************************************
         *            General settings favicon & logo
         ******************************************************************/
        $(document).ready(function () {
            $('.settings-file').on('change', function () {

                var dataTarget = $(this).attr('data-target'),
                    that = this,
                    template = '';

                if (that.files && that.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        template = '<div class="sommerce-settings__theme-imagepreview"><img src="'+e.target.result+'" alt="'+that.files[0].name+'" id="setting-logo__preview"></div>'
                        $(dataTarget).html(template);
                    };

                    reader.readAsDataURL(that.files[0]);
                }
            });
        });
        /*****************************************************************************************************
         *                      Delete (mark as deleted) Page
         *****************************************************************************************************/
        var $modalPay = $('#delete-modal-pay'),
            $modalLoader = $modalPay.find('.modal-loader'),
            $buttonDelete = $modalPay.find('#feature-delete'),
            actionUrl,
            successRedirectUrl;

        $buttonDelete.on('click', function(){
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "POST",
                success: function (data, textStatus, jqXHR){
                    //Success
                    _.delay(function(){
                        $(location).attr('href', successRedirectUrl);
                    }, 500);
                },
                error: function (jqXHR, textStatus, errorThrown){
                    $modalLoader.addClass('hidden');
                    $modalPay.modal('hide');
                    console.log('Error on service save', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $modalPay.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget);
            actionUrl = button.data('action_url');
            successRedirectUrl = $modalPay.data('success_redirect');
        });

        $modalPay.on('hidden.bs.modal', function (){
            actionUrl = null;
        });
    }
};