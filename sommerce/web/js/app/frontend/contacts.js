customModule.contactsForm = {
    run: function (params) {
        /******************************************************************
         *            Contact form
         ******************************************************************/
        $('#contactForm').on('click', '.block-contactus__form-button', function (e) {
            e.preventDefault();
            var form = $('#contactForm');
            var errorBlock = $('#contactFormError', form);
            var actionUrl = params.action;
            var csrfParam = $('meta[name="csrf-param"]').attr("content");
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            var btn = $('.block-contactus__form-button');

            var postData = form.serializeArray();
            postData.push({name: csrfParam, value: csrfToken});

            btn.prop('disabled', true);

            $.ajax({
                url: actionUrl,
                type: "POST",
                dataType: 'json',
                data: postData,
                success: function (response) {
                    errorBlock.removeClass('alert-danger');
                    errorBlock.addClass('alert-success');
                    errorBlock.html(response.data.message);
                    form.trigger('reset');
                    if (window.grecaptcha) grecaptcha.reset();
                    btn.removeAttr('disabled');
                },
                error: function (jqXHR) {
                    errorBlock.removeClass('alert-success');
                    errorBlock.addClass('alert-danger');
                    errorBlock.html(jqXHR.responseJSON.error_message);
                    btn.removeAttr('disabled');
                }
            });
        });
    }
};
