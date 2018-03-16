customModule.superadminTicketsController = {
    run : function(params) {
        $('#domainsSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#domainsSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('#createTicket').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var modal = $('#createTicketModal');
            var modalContainer = $('.modal-body', modal);
            var form = $('#createTicketForm');
            var errorBlock = $('#createTicketError', form);

            $('input[type="text"], textarea', form).val('');
            $('select', form).prop('selectedIndex', 0);

            form.attr('action', action);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createTicketButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createTicketForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createTicketModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};