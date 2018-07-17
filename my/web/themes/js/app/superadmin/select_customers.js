customModule.superadminSelectCustomerController = {
    run : function(params) {
        $('div.customers-select').on("keyup", "input", function (e) {
            if (e.which !== 0 || e.keyCode == '8') {
                var $input = $(this);
                var $select = $input.closest('.customers-select').find('select');
                var query = $input.val();
                if (query.length >= 3 || query.length === 0 || e.keyCode == '8') {
                    fetchData($select.data('action'), query, $select, false);
                }
            }
        });

        function fetchData(action, query, $select, refresh) {
            $.ajax({
                url: action,
                type: 'GET',
                dataType: 'json',
                data: {'email': query},
                success: function (dataList) {
                    updateSelect(dataList, $select, refresh);
                }
            });
        }

        function updateSelect(dataList, $select, refresh) {
            var options = $select.find('option');
            var count = options.length;
            options.remove();
            for (var i = 0; i < dataList.length; i++) {
                $select.append($("<option></option>")
                    .attr({
                        'data-tokens': dataList[i].email,
                        'value': dataList[i].id
                    })
                    .text(dataList[i].email)
                );
            }
            if (count && dataList.length || refresh) {
                $('.selectpicker.customers-select').selectpicker('refresh');
            }
        }

        $( "select.customers-select" ).on( "customers:refresh", function( event ) {
            var $input = $('div.customers-select').find('input');
            var $select = $(this);
            $input.val('');
            fetchData($select.data('action'), '', $select, true);
        });

        $('.bootstrap-select').on('click','.dropdown-menu li', function(e) {
            var $select = $('select.customers-select');
            $select.find('option').removeAttr('selected');
            var index = $(this).data('original-index');
            var option = $select.find('option').eq(index);
            $select.val(option.val()).change();
        });
    }
};
