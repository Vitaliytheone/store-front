customModule.contactsForm = {
    run: function (params) {
        /******************************************************************
         *            Contact form
         ******************************************************************/
        $('#contactForm').on('click', '.block-contactus__form-button', function (e) {
            e.preventDefault();
            var form = $('#contactForm');
            var errorBlock = $('#contactFormError', form);
            var actionUrl = params.contact_action_url;
            var csrfParam = $('meta[name="csrf-param"]').attr("content");
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            var btn = $('.block-contactus__form-button');

            var postData = form.serializeArray();
            postData.push({name: csrfParam, value: csrfToken});

            if (btn.hasClass('active')) {
                return;
            }

            btn.addClass('has-spinner');
            $('.spinner', btn).remove();

            btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                dataType: 'json',
                data: postData,
                beforeSend: function () {
                    btn.addClass('active');
                },
                success: function (response) {
                    btn.removeClass('active');
                    $('.spinner', btn).remove();
                    errorBlock.removeClass('alert-danger');
                    errorBlock.addClass('alert-success');
                    errorBlock.html(response.data.message);
                    form.trigger('reset');
                    if (window.grecaptcha) grecaptcha.reset();
                },
                error: function (jqXHR) {
                    errorBlock.removeClass('alert-success');
                    errorBlock.addClass('alert-danger');
                    errorBlock.html(jqXHR.responseJSON.error_message);
                    btn.removeClass('active');
                    $('.spinner', btn).remove();
                }
            });
        });
    }
};
