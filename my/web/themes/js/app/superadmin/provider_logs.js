customModule.superadminProviderLogsController = {
    run : function(params) {
        $('#logsSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#logsSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });
    }
};