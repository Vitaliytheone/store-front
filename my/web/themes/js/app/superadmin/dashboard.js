customModule.superadminDashboardController = {
    run: function (params) {
        var data = {};

        function createTable(items) {
            $tbody = $('.table-mobile tbody');
            $tbody.children().remove();
            var content = templates['dashboard/entity_table']({'titles' : params['titles'], 'items':items});
            $tbody.append(content);

        }

        $('.dashboard2-card__row .col').click(function (e) {
            var action = $(this).data('action');
            if ($(this).hasClass('dashboard2-card__active') || !action) {
                return;
            }
            $('.dashboard2-card__active').removeClass('dashboard2-card__active');
            $(this).addClass('dashboard2-card__active');
            var $tbody = $('.table-mobile tbody');
            $('.table-mobile').hide();
            $tbody.children().remove();
            $('#loader').show();
            fetchData(action);
        });

        function fetchData(action) {
            $.ajax({
                url: action,
                type: 'GET',
                dataType: 'json',
                data: data,
                success: function (dataList) {
                    createTable(dataList);
                },
                complete: function() {
                    $('#loader').hide();
                    $('.table-mobile').show();
                }
            });
        }

        function fetchBalanceService(action, $item) {
            $.ajax({
                url: action,
                type: 'GET',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data['status'] === 'error') {
                        var $badge = '<a href="#" data-action="'
                            + action + '" class="badge badge-danger">'
                            + params.error + '</a>';

                        $item.html($item.html() + $badge);
                    } else {
                        $item.html($item.html() + data['data']['balance']);
                    }
                },
                error: function (error) {
                    var $badge = '<a href="#" data-action="'
                        + action + '" class="badge badge-danger">'
                        + params.error + '</a>';

                    $item.html($item.html() + $badge);
                },
                complete: function () {
                    $item.find('.fa-spinner').hide();
                }
            });
        }

        $('.dashboardService').each(function(index, el) {
            var $item = $(el);
            fetchBalanceService($item.data('action'), $item);
        });

        $(".balances-line").on("click", ".badge", function (e) {
            var $this = $(e.currentTarget);
            $this.closest('.dashboardService').find('.fa-spinner').show();
            var action = $this.data('action');
            var $service = $this.closest('.dashboardService');
            fetchBalanceService(action, $service);
            e.preventDefault();
            $this.remove();
        });

    }
};




