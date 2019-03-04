/******************************************************************
 *            Contact form
 ******************************************************************/
$('#contactForm').on('click', '.block-contactus__form-button', function (e) {
    e.preventDefault();
    var form = $('#contactForm');
    var errorBlock = $('#contactFormError', form);
    var actionUrl = '/site/contact-us';

    $.ajax({
        url: actionUrl,
        async: false,
        type: "POST",
        dataType: 'json',
        data: form.serialize(),
        success: function (data) {
            if (data.error == false) {
                errorBlock.removeClass('alert-danger');
                errorBlock.addClass('alert-success');
                errorBlock.html(data.success);
            } else {
                errorBlock.removeClass('alert-success');
                errorBlock.addClass('alert-danger');
                errorBlock.html(data.error_message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error on send', textStatus, errorThrown);
        }
    });
});
