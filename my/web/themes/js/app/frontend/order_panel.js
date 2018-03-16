customModule.orderPanelController = {
    run : function(params) {
        $('.has_domain').change(function(e) {
            e.preventDefault();

            var radio = $("input.has_domain:checked").val();

            if (1 == radio) {
                $('#orderPanelBlock').removeClass('hidden');
                $('#orderDomainBlock').addClass('hidden');
                $('#orderNote').removeClass('hidden');

                $('#domain').val('').prop('readonly', false);
            } else {
                $('#orderDomainBlock').removeClass('hidden');
                $('#orderPanelBlock').addClass('hidden');
                $('#searchResult').addClass('hidden');
                $('#searchResultContainer').html('');
                $('#orderNote').addClass('hidden');
            }

            return false;
        });

        $('#order-panel-form').on('submit', function() {
            if ($("#orderPanelBlock").hasClass('hidden')) {
                $('#orderDomainModal').modal('hide');
                $('#searchDomainSubmit').trigger('click');
                return false;
            }

            return true;
        });
    }
};