customModule.superadminDbHelperController = {
    run: function (params) {
        $('.query_content').css("white-space", "pre-wrap");
        $('.query_content').css("white-space", "-moz-pre-wrap");
        $('.query_content').css("white-space", "-pre-wrap");
        $('.query_content').css("white-space", "-o-pre-wrap");
        $('.query_content').css("word-wrap", "break-word");

        var strReplace = 'db_name';

        $('.db_name').change(function(e) {
            e.preventDefault();

            var str = $('.query_input').val();

            var newStr = str.replace(strReplace, $(this).val());
            strReplace = $(this).val();

            $('.query_input').val(newStr);
            $('.query_content').text(newStr);
            console.log(newStr);
        });

        $('.query_input').keyup(function() {
            var str = $(this).val();

            $('.query_content').text(str);
        });
    }
}