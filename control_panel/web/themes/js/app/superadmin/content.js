customModule.superadminContentController = {
    run : function(params) {
        $('.edit-content').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editContentModal');
            var form = $('#editContentForm');
            var errorBlock = $('#editContentError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editcontentform-name', form).val(details.name);
            $('#editcontentform-text', form).val(details.text);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editContentButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editContentForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editContentModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};