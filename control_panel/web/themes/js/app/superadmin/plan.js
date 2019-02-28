customModule.superadminPlanController = {
    run : function(params) {
        $(document).on('click', '#createPlan', function(e) {
            e.preventDefault();

            var link = $(this);
            var form = $('#createPlanForm');
            var modal = $('#createPlanModal');
            var errorBlock = $('#createPlanError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]').prop('checked', true);
            $('select', modal).prop('selectedIndex', 0);

            modal.modal('show');

            return false;
        });

        $('.edit-plan').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editPlanModal');
            var form = $('#editPlanForm');
            var errorBlock = $('#editPlanError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editplanform-title', form).val(details.title);
            $('#editplanform-price', form).val(details.price);
            $('#editplanform-description', form).val(details.description);
            $('#editplanform-of_orders', form).val(details.of_orders);
            $('#editplanform-before_orders', form).val(details.before_orders);
            $('#editplanform-up', form).val(details.up);
            $('#editplanform-down', form).val(details.down);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editPlanButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPlanForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editPlanModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '#createPlanButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createPlanForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createPlanModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};