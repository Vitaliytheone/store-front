customModule.superadminTicketsController = {
    run : function(params) {
        $('#search').on('click', function (e) {
            e.preventDefault();

            var form = $('#ticketsSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('#new-ticket').click(function (e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var modal = $('#createTicketModal');
            var form = $('#createTicketForm');
            $('input[type="text"], textarea', form).val('');
            form.attr('action', action);
            $('select.customers-select').trigger('customers:refresh');
            $('select', form).prop('selectedIndex', 0);
        });
        
        $(document).on('click', '#createTicketButton', function (e) {
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

        $('.status-tab').click(function(e) {
            window.location = $(this).attr('href');
        });
    }
};