$('.dropdown-collapse').on('click', function (event) {
    event.preventDefault();
    event.stopPropagation();
    if(($(this).next()).hasClass('show')){
        $($(this).attr('href')).collapse('hide');
    }else{
        $($(this).attr('href')).collapse('show');
    }
});



$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});


$(document).ready(function () {
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;

        input.addEventListener('change', function (e) {
            var fileName = '';
            if (this.files && this.files.length > 1){
                fileName = ( this.getAttribute('data-multiple-caption') || '' ).replace('{count}', this.files.length);
            }else {
                fileName = e.target.value.split('\\').pop();
            }
            if (fileName) {
                //label.querySelector('span').innerHTML = fileName;
                if (this.files && this.files[0]) {

                    var reader = new FileReader();

                    reader.onload = function (e) {
                        let template = `<div class="sommerce-settings__theme-imagepreview">
                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>
                              <img src="${e.target.result}" alt="...">
                          </div>`;
                        $('#image-preview').html(template);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            }else {
                //label.innerHTML = labelVal;
            }
        });
        $(document).on('click', '.sommerce_v1.0-settings__delete-image', function(e) {
            $('#image-preview').html('<span></span>');
            input.value = '';
        });
    });

});


/* Edit page */
$(document).ready(function () {

    if($('.edit-seo__title').length > 0) {

        let seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

        for (let i = 0; i < seoEdit.length; i++) {
            $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
            $("#" + seoEdit[i]).on('input', function (e) {
                if (i == 2) {
                    $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
                } else {
                    $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                    $('.' + seoEdit[i]).text($(e.target).val());
                }
            });
        }
    }
});


$(document).ready(function () {
    $('#select-menu-link').change(function () {
        $('.hide-link').hide();
        let val = $("#select-menu-link option:selected").val();
        $('.link-'+val).fadeIn()
    });
});


$(document).ready(function () {
    $(document).on('click', '.delete-properies', function () {
        $(this).parent().remove();
    });
    $(document).on('click', '.add-properies', function () {
        let inputProperties = $('.input-properties').val();
        if(inputProperties.length) {
            $('.list-preperties').append(`<li class="list-group-item">${inputProperties} <span class="fa fa-times delete-properies"></span></li>`)
            $('.input-properties').val('');
        }
    });
});


