customModule.contactsFrontend = {
    run: function (params) {
        $('#contactForm').on('click', '.block-contactus__form-button', function (e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#contactForm');
            var errorBlock = $('#contactFormError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback: function (response) {

                    if ('success' == response.status) {
                        $('#editCustomerModal').modal('hide');
                        location.reload();
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });
    }
};
