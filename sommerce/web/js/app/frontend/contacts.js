/******************************************************************
 *            Contact form
 ******************************************************************/
$('#contactForm').on('click', '.block-contactus__form-button', function (e) {
    e.preventDefault();
    var btn = $(this);
    var form = $('#contactForm');
    var errorBlock = $('#contactFormError', form);
    var actionUrl = '/page/contact-us';

    errorBlock.addClass('hidden');

    $.ajax({
        url: actionUrl,
        async: false,
        type: "POST",
        dataType: 'json',
        data: form.serialize(),
        success: function (data) {
            errorBlock.removeClass('hidden');
            if (data.error == false) {
                errorBlock.removeClass('alert-danger');
                errorBlock.addClass('alert-success');
                errorBlock.html(data.success);
                console.log('Success', data);
            } else {
                errorBlock.removeClass('alert-success');
                errorBlock.addClass('alert-danger');
                errorBlock.html(data.error_message);
                console.log('Error', data);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error on update', jqXHR, textStatus, errorThrown);
        }
    });
});
