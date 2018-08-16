customModule.superadminDbHelperController = {
    run: function (params) {
        $('.query_content').css("white-space", "pre-wrap");
        $('.query_content').css("white-space", "-moz-pre-wrap");
        $('.query_content').css("white-space", "-pre-wrap");
        $('.query_content').css("white-space", "-o-pre-wrap");
        $('.query_content').css("word-wrap", "break-word");


        $('.db_name').change(function(e) {
            e.preventDefault();

            var str = $('.query_input').val();

            var newStr = str.replace(/(db_name)|(panel_\w+)|(store_\w+)/g, $(this).val());
            strReplace = $(this).val();


            $('.query_content').text(newStr);
            console.log(newStr);
        });

        $('#dbHelperButton').click(function() {
            var str = $('.query_input').val();
            var newStr = str.replace(/(db_name)|(panel_\w+)|(store_\w+)/g, $('.db_name').val());

            $('.query_content').text(newStr);
        });
    }
}