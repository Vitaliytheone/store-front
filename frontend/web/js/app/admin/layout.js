customModule.adminLayout = {
    run : function(params) {
        var self = this;

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
        });

        $(document).ready(function () {
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
        });

        /* Edit page */
        $(document).ready(function () {

            if ($('.edit-seo__title').length > 0) {
                (function () {

                    var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                    var _loop = function _loop(i) {
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
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
        });

        $(document).ready(function () {
            $('#select-menu-link').change(function () {
                $('.hide-link').hide();
                var val = $("#select-menu-link option:selected").val();
                $('.link-' + val).fadeIn();
            });
        });

    }
};