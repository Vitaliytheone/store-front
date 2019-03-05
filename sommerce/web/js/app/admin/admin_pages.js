customModule.adminPages = {
    run: function (params) {
        var existingUrls = params['existingUrls'];

        $('.dropdown-collapse').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if ($(this).next().hasClass('show')) {
                $($(this).attr('href')).collapse('hide');
            } else {
                $($(this).attr('href')).collapse('show');
            }
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('.sommerce-dropdown__delete-cancel').click(function () {
                $(".sommerce-dropdown__delete").hide();
            });
        });

        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
                    fileName = e.target.value.split('\\').pop();
                }
                if (fileName) {
                    //label.querySelector('span').innerHTML = fileName;
                    if (this.files && this.files[0]) {

                        var reader = new FileReader();

                        reader.onload = function (e) {
                            var template = '<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="' + e.target.result + '" alt="...">\n                          </div>';
                            $('#image-preview').html(template);
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                } else {
                    //label.innerHTML = labelVal;
                }
            });
            $(document).on('click', '.sommerce_v1.0-settings__delete-image', function (e) {
                $('#image-preview').html('<span></span>');
                input.value = '';
            });
        });


        if ($('.edit-seo__title').length > 0) {
            (function () {

                var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                var _loop = function _loop(i) {
                    if ($("#" + seoEdit[i]).length) {
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                    }
                    $("#" + seoEdit[i]).on('input', function (e) {
                        if (i == 2) {
                            $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
                        } else {
                            $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                            $('.' + seoEdit[i]).text($(e.target).val());
                        }
                    });
                };

                for (var i = 0; i < seoEdit.length; i++) {
                    _loop(i);
                }
            })();
        }



        $('#select-menu-link').change(function () {
            $('.hide-link').hide();
            var val = $("#select-menu-link option:selected").val();
            $('.link-' + val).fadeIn();
        });


        $('#btn-new-page').click(function(e){
            var flag = true;
            var $this = $(this);
            var $name = $('#editpageform-name');
            $name.val('');
            $name.trigger('input');
            $('#check-visibility').prop('checked', 'checked');
            $('.btn-modal-delete').hide();
            var $keyword = $('#edit-seo__meta-keyword');
            $keyword.val('');
            $keyword.trigger('input');
            var $meta = $('#edit-seo__meta');
            $meta.val('');
            $meta.trigger('input');
            var  $title = $('#edit-seo__title')
            $title.val('');
            $title.trigger('input');
            $('#seo-block').removeClass('show');

            $name.on('input', function(e) {
                var generatedUrl = custom.generateUrlFromString($(this).val());
                generatedUrl = custom.generateUniqueUrl(generatedUrl, existingUrls);
                if (flag) {
                    var $url = $('#edit-seo__url');
                    $url.val(generatedUrl);
                    $url.trigger('input', true);
                }
            });

            var $url = $('#edit-seo__url');
            $url.val('');
            $url.trigger('input', true);

            $url.on('input', function (e, data) {
                if (!data) {
                    flag = false;
                }
            });

            $('#pageForm').attr('action', $this.data('action'));
            $('#exampleModalLabel').text($this.data('modal-title'));
            $('#page-submit').text($this.data('modal-title'));

        });


        $('#pageForm').submit(function (event){
            event.preventDefault();
            var btn = $('#page-submit');
            var form = $(this);

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback: function() {
                    $('#modal-create-page').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.edit-page').click(function(e) {
            e.preventDefault();
            var $this =  $(this);
            var page = $this.data('page');
            var $name = $('#editpageform-name');
            $name.off('input');
            $name.val(page.name);

            if (parseInt(page.visibility)) {
                $('#check-visibility').prop('checked', 'checked');

            } else {
                $('#check-visibility').prop('checked', false);
            }

            if (page['can_delete']) {
                $('.btn-modal-delete').show();
            }

            var $keyword = $('#edit-seo__meta-keyword');
            $keyword.val(page.seo_keywords);
            $keyword.trigger('input');
            var $meta = $('#edit-seo__meta');
            $meta.val(page.seo_description);
            $meta.trigger('input');
            var  $title = $('#edit-seo__title')
            $title.val(page.seo_title);
            $title.trigger('input');
            $('#seo-block').removeClass('show');

            var $url = $('#edit-seo__url');
            $url.val(page.url);
            $url.trigger('input');

            $('#pageForm').attr('action', $this.data('action'));
            $('#exampleModalLabel').text($this.data('modal-title'));
            $('#page-submit').text($this.data('modal-title'));
            $('.delete-page').data('params', page);

            $('#modal-create-page').modal('show');
        });

        $('.delete-page').click(function(e) {

            var $related = $(this);
            var data = $related.data('params');

            if (!data['can_delete']) {
                e.preventDefault();
                return false;
            }

            var queryParams = {};
            queryParams.id = data.id;

            custom.confirm(params['confirm_message'], '', {}, function () {
                custom.sendBtn($related, {
                    data: addTokenParams(queryParams),
                    type: 'POST',
                    callback: function () {
                        location.reload();
                    }
                });
                return false;
            });
            e.preventDefault();
        });

        $('.duplicate-page').click(function(e) {
            e.preventDefault();
            var $related = $(this);
            var page = $related.data('page');
            var $modal = $('#modal-duplicate');
            $modal.data('page', page);
            $('#feature-duplicate').attr('href', $related.data('action'));
            $modal.modal('show');
            return false;
        });

        $('#feature-duplicate').click(function(e){
            e.preventDefault();
            var $this = $(this);
            var page = $('#modal-duplicate').data('page');
            var queryParams = {}
            queryParams.id = page.id;

            var generatedUrl = custom.generateUrlFromString(page.name);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, existingUrls);
            queryParams.url = generatedUrl;
            custom.sendBtn($this, {
                data: addTokenParams(queryParams),
                type: 'POST',
                callback: function () {
                    location.reload();
                }
            });

            return false;
        });

        function addTokenParams($obj) {
            var csrfToken = $('meta[name="csrf-token"]').attr("content"),
                csrfParam = $('meta[name="csrf-param"]').attr("content");

            $obj[csrfParam] = csrfToken;

            return $obj;
        }
    }



};
