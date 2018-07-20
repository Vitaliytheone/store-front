customModule.superadminSelectCustomerController = {
    run : function(params) {
        console.log('superadminSelectCustomerController');
        $('div.customers-select').on("keyup", "input", function (e) {
            console.log('customers-select');
            if (e.which !== 0 || e.keyCode == '8') {
                console.log('e.which' + e.which);
                var $input = $(this);
                var $select = $input.closest('.customers-select').find('select');
                var query = $input.val();
                if (query.length >= 3 || query.length === 0 || e.keyCode == '8') {
                    fetchData($select.data('action'), query, $select, false);
                }
            }
        });

        function fetchData(action, query, $select, refresh) {
            console.log('fetchData ' + action);
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
            console.log('updateSelect ');
            var options = $select.find('option:not(:selected)');
            var count = options.length;
            options.remove();
            for (var i = 0; i < dataList.length; i++) {
                if ($select.val() != dataList[i].id) {
                    $select.append($("<option></option>")
                        .attr({
                            'data-tokens': dataList[i].email,
                            'value': dataList[i].id
                        })
                        .text(dataList[i].email)
                    );
                }
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


        $("select.customers-select" ).on( "customers:add", function( event, data ) {
            var $select = $(this);
            var options = $select.find('option');
            if (options.length == 10) {
                options.eq(9).remove();
            }
            $select.append($("<option></option>")
                .attr({
                    'data-tokens': data.email,
                    'value': data.id
                })
                .text(data.email)
            );
            $select.val(data.id);
            $('.selectpicker.customers-select').selectpicker('refresh');
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
