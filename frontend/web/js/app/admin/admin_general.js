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
    }
};