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

        $('.create-note').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var modal = $('#createNotesModal');
            var form = $('#createNoteForm');
            var errorBlock = $('#createNoteError');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            form.attr('action', action);
            modal.modal('show');

            return false;
        });

        $('.edit-note').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var modal = $('#editNotesModal');
            var form = $('#editNoteForm');
            var errorBlock = $('#editNoteError');
            var note = link.data('note');

            errorBlock.addClass('hidden');
            errorBlock.html('');
            $('.note_content').val(note);

            form.attr('action', action);
            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createNoteButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createNoteForm');
            var errorBlock = $('#createNoteError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#createNotesModal').modal('hide');
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

        $(document).on('click', '#editNoteButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editNoteForm');
            var errorBlock = $('#editNoteError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#editNotesModal').modal('hide');
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