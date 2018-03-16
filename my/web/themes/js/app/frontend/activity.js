customModule.activityController = {
    filters : undefined,
    highCharts : undefined,
    searchForm: undefined,
    activityLogContainer: undefined,
    itemsContainer: undefined,
    paginationContainer: undefined,
    errorContainer: undefined,
    viewContainer: undefined,
    run : function(params) {
        var self = this;

        self.viewContainer = $('#page-wrapper');
        self.activityLogContainer = $('#activityLogContainer');
        self.searchForm = $('#activitySearch');
        self.itemsContainer = $('#itemsContainer');
        self.paginationContainer = $('#paginationContainer');
        self.highCharts = Highcharts;
        self.filters = params.filters;
        self.errorContainer = $('#errorContainer');

        self.hideError();
        self.updateData();


        $('#date-from').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#date-to').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: new Date()
        });

        $('.selectpicker').selectpicker({
            style: 'btn-default',
            size: 'auto',
        }).on('rendered.bs.select', function (e) {
            var element = $(this);
            var block = element.parent('.bootstrap-select');

            if (!$('option:checked', element).length) {
                $('.filter-option', block).html(element.data('title'));
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var link = $(this);
            self.updateData(link.attr('href'), ['items']);
            return false;
        });
    },
    updateData: function(url, actions) {
        var self = this;
        var filters = {};
        if ('undefined' == typeof url) {
            url = document.location.protocol +"//"+ document.location.hostname + document.location.pathname;
            filters = $.extend({}, true, self.filters);
        }

        if ('undefined' != typeof actions) {
            filters.actions = actions.join(',');
        }

        self.hideError();
        self.showLoader(self.activityLogContainer);

        $.ajax({
            url: url,
            data: filters,
            method: 'GET',
            success: function (response) {
                self.scrollTop();

                self.hideLoader(self.activityLogContainer);

                if (response.error) {
                    self.showError(response.error);

                    return;
                }

                if (response.items) {
                    self.itemsContainer.html(response.items);
                }

                if (response.pagination) {
                    self.paginationContainer.html(response.pagination);
                }

                if (response.activity) {
                    self.initHighCharts(response.interval, response.activity);
                }

                if (response.accounts) {
                    var select = $('#account', self.searchForm);
                    var selectedValues = 'undefined' != typeof self.filters.account ? self.filters.account : [];
                    select.html('');

                    $.each(response.accounts, function(key, value) {
                        select
                            .append($("<option></option>")
                                .attr("value", key)
                                .text(value));
                    });

                    $.each(selectedValues, function(i, e) {
                        $("option[value='" + e + "']", select).prop("selected", true);
                    });

                    select.selectpicker('refresh');
                }

                if (response.events) {
                    var select = $('#event', self.searchForm);
                    var selectedValues = 'undefined' != typeof self.filters.event ? self.filters.event : [];
                    select.html('');

                    $.each(response.events, function(key, groupValue) {
                        var group = $("<optgroup></optgroup>")
                            .attr("label", groupValue.title);

                        $.each(groupValue.events, function(key, eventValue) {
                            group
                                .append($("<option></option>")
                                    .attr("value", key)
                                    .text(eventValue));
                        });

                        select.append(group);
                    });

                    $.each(selectedValues, function(i, e) {
                        $("option[value='" + e + "']", select).prop("selected", true);
                    });

                    select.selectpicker('refresh');
                }
            },
            statusCode: {
                403: function() {
                    location.reload();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                self.showError('Can not load activity log data.');
                self.hideLoader(self.activityLogContainer);
            }
        });
    },
    initHighCharts: function(interval, activity)
    {
        var self = this;
        var data = [];

        $.each(activity, function (key, value) {
            console.log(value);
            data.push([parseInt(value.point) * 1000, parseInt(value.count)]);
        });
        console.log(data);

        var highChartsOptions = {
            chart: {
                type: 'area',
                zoomType: false,
                marginTop: 0,
                marginLeft: 0,
                marginRight: 0,
                marginBottom: 0,
            },
            title: false,
            subtitle: false,
            xAxis: {
                type: 'datetime',
                gridLineWidth: 1,

                tickInterval: parseInt(interval) * 1000,
                tickWidth: 0,
                labels: {
                    align: 'left',
                    x: 3,
                    y: -3
                }
            },
            yAxis: {
                title: false,
                minPadding: 0,
                maxPadding: 0,
                min: 0,
                minRange : 0.1,
                labels: {
                    enabled: false
                }
            },
            legend: false,
            plotOptions: {
                series: {
                    fillOpacity: 0.2,
                },
                area: {
                    marker: {
                        radius: 2
                    },
                    lineWidth: 2,
                    threshold: null
                }
            },

            series: [{
                name: 'Events',
                data: data
            }]
        };

        self.highCharts.chart('events-count', highChartsOptions);
    },
    showLoader: function(contaier) {
        var self = this;
        if ('undefined' == typeof contaier || !contaier.length) {
            return;
        }

        self.hideLoader(contaier);

        contaier.append($("<div></div>").attr("id", 'cover'));
    },
    hideLoader: function(contaier) {
        if ('undefined' == typeof contaier || !contaier.length) {
            return;
        }

        $('#cover', contaier).remove();
    },
    hideError: function()
    {
        var self = this;

        $('.message-container', self.errorContainer).text('');
        self.errorContainer.addClass('hidden');
    },
    showError: function(message)
    {
        var self = this;

        $('.message-container', self.errorContainer).text(message);
        self.errorContainer.removeClass('hidden');
    },
    scrollTop: function() {
        var self = this;
        $("html, body").animate({
            scrollTop: self.viewContainer.offset().top
        }, 200);
    }
};