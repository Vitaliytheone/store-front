customModule.superadminProvidersController = {
    run : function(params) {
        $('#providersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#providersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $(document).on('click', '.show-panels', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#providerPanelsModal');
            var container = $('.modal-body', modal);
            var header = link.data('header');

            $('.modal-title', modal).text(header);

            container.html('<img src="/themes/img/ajax-loader.gif" border="0">');
            modal.modal('show');
            var projects = link.data('projects');

            if (!projects || !projects.length) {
                container.html('');
                return false;
            }

            var content = [];
            $.each(projects, function (index, project) {
                content.push('<div class="row"> <div class="col-md-12"> ' + project.site + ' </div> </div>');
            });

            container.html(content.join(''));

            return false;
        });

        var defaultStyle = {
            "background-repeat": "no-repeat",
            "background-position": "100% 50%"
        };

        $('.query-sort').data("sorter", false);
        $('.query-sort').css(defaultStyle);
        $('.sort_default').css("background-image", "url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE0IDIwIj48cGF0aCBkPSJNMTQgMTNsLTIuNS0yLjVMNyAxNWwtNC41LTQuNUwwIDEzbDcgN3pNMTQgNy41TDExLjUgMTAgNyA1LjUgMi41IDEwIDAgNy41bDctN3oiLz48L3N2Zz4=)");
        $('.no_sort').data("sorter", false);
        $('.sort_asc').css("background-image", "url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMiIgaGVpZ2h0PSIxMiIgdmlld0JveD0iMCAwIDE0IDE0Ij48cGF0aCBkPSJNMTQgOS41TDExLjUgMTIgNyA3LjUgMi41IDEyIDAgOS41bDctN3oiLz48L3N2Zz4=)");
        $('.sort_desc').css("background-image", "url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMiIgaGVpZ2h0PSIxMiIgdmlld0JveD0iMCAwIDE0IDE0Ij48cGF0aCBkPSJNMTQgNWwtMi41LTIuNS00LjUgNC41LTQuNS00LjVMMCA1IDcgMTJ6Ii8+PC9zdmc+)");

    }
};