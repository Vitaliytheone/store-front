customModule.orderController = {
    run : function(params) {
        $('.has_domain').change(function(e) {
            e.preventDefault();

            var radio = $("input.has_domain:checked").val();

            if (1 == radio) {
                $('#orderBlock').removeClass('hidden');
                $('#orderDomainBlock').addClass('hidden');
                $('#orderNote').removeClass('hidden');

                $('#domain').val('').prop('readonly', false);
            } else {
                $('#orderDomainBlock').removeClass('hidden');
                $('#orderBlock').addClass('hidden');
                $('#searchResult').addClass('hidden');
                $('#searchResultContainer').html('');
                $('#orderNote').addClass('hidden');
            }

            return false;
        });

        $('#order-form').on('submit', function() {
            if ($("#orderBlock").hasClass('hidden')) {
                $('#orderDomainModal').modal('hide');
                $('#searchDomainSubmit').trigger('click');
                return false;
            }

            return true;
        });
    }
};