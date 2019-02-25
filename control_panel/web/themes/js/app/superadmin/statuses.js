customModule.superadminStatusesController = {
    run : function(params) {

        $(document).ready(function () {
            $('#data-table').DataTable({
                'bPaginate': false,
                'bLengthChange': false,
                'bFilter': true,
                'bInfo': false,
                'bAutoWidth': false
            });
        });

    }
};