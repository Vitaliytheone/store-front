customModule.superadminDbHelperController = {
    run: function (params) {
        if ($('.query_content').height() > $(window).height()) {
            $('.query_content').css("overflow", "scroll");
            $('.query_content').css("height", $(window).height());
        }
    }
}
