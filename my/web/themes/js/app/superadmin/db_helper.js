customModule.superadminDbHelperController = {
    run: function (params) {
        $('.query_content').css("white-space", "pre-wrap");
        $('.query_content').css("white-space", "-moz-pre-wrap");
        $('.query_content').css("white-space", "-pre-wrap");
        $('.query_content').css("white-space", "-o-pre-wrap");
        $('.query_content').css("word-wrap", "break-word");

        if ($('.query_content').height() > $(window).height()) {
            $('.query_content').css("overflow", "scroll");
            $('.query_content').css("height", $(window).height());
        }
    }
}