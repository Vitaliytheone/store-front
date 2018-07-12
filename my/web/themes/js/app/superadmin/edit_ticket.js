customModule.superadminTicketsEditController = {
    run: function (params) {
        function mobileCollapse()  {
            var screenWidth = $(window).width();
            if(screenWidth < 768) {
                $('.ticket-info__block-header').addClass('collapsed');
                $('.ticket-info__block').removeClass('show');
            }
        }

        $(window).resize(function() {
            mobileCollapse();
        });

        $('.ticket-message__card-link').click(function(e) {
           e.preventDefault();
        });

        $(document).on('click', '.btn-default', function(e) {
            e.preventDefault();
            $(this).blur();
        });

        $('.ticket-message__card-link.delete-link').click(function(e) {
            e.preventDefault();
            return false;
        });

        $('.open-edit-modal').click(function (e) {
            var $el = $(this);
            $('#edit-message-ticketId').val($el.data('ticket'));
            $('#edit-message').val($el.data('id'));
            $('#edit-message-content').val($el.data('content'));
        });

        $('#assigned-toggle').click(function (e) {
            var $el = $(this);
            if (!$el.parent().find('option').length) {
                e.stopPropagation();
                $el.blur();
            }
        });

        $(document).on('click', '#modal-save-edit', function(e) {
            e.preventDefault();
            var form = $('#edit-message-form');
            var btn = $('#modal-save-edit');
            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#edit-message-modal').modal('hide');
                    location.reload();
                }
            });
            $('#edit-message-content').val('') ;
        });
    }
};