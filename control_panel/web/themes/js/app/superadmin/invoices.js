customModule.superadminInvoicesController = {
    run : function(params) {

        $(document).on('click', '.cancel-menu', function(e) {
            e.preventDefault();

            var link = $(this);

            custom.confirm(link.data('confirm-message'), '', function() {
                location.href = link.attr('href');
            });

            return false;
        });

        $('#invoicesSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#invoicesSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.add-payment').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#addPaymentForm');
            var modal = $('#addPaymentModal');
            var errorBlock = $('#addPaymentError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('select', form).prop('selectedIndex', 0);

            modal.modal('show');

            return false;
        });

        $('.edit-credit').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#editCreditForm');
            var modal = $('#editCreditModal');
            var errorBlock = $('#editCreditError', form);
            var details = link.data('details');

            $('input[type="text"]', form).val('');
            $('select', form).prop('selectedIndex', 0);

            $('#editinvoicecreditform-credit', form).val(details.credit);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $('.add-earnings').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#addEarningsForm');
            var modal = $('#addEarningsModal');
            var errorBlock = $('#addEarningsError', form);
            var details = link.data('details');

            $('input[type="text"]', form).val('');
            $('select', form).prop('selectedIndex', 0);

            $('#addinvoiceearningsform-credit', form).val(details.credit);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $('.edit-invoice').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#editInvoiceForm');
            var modal = $('#editInvoiceModal');
            var errorBlock = $('#editInvoiceError', form);
            var details = link.data('details');

            $('input[type="text"]', form).val('');
            $('select', form).prop('selectedIndex', 0);

            $('#editinvoiceform-total', form).val(details.total);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editInvoiceButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editInvoiceForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editInvoiceModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });


        $(document).on('click', '#addPaymentButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addPaymentForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#addPaymentModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '#editCreditButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editCreditForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editCreditModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '#addEarningsButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addEarningsForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#addEarningsModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('#createInvoice').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var modal = $('#createInvoiceModal');
            var modalContainer = $('.modal-body', modal);
            var form = $('#createInvoiceForm');
            var errorBlock = $('#createInvoiceError', form);

            $('input[type="text"], textarea', form).val('');
            $('select', form).prop('selectedIndex', 0);

            form.attr('action', action);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createInvoiceButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createInvoiceForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createInvoiceModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        custom.clipboard('.copy', {
            text: function (trigger) {
                return trigger.getAttribute('data-link');
            }
        });
    }
};