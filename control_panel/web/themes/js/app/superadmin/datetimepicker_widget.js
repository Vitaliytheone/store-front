customModule.superadminDatetimepickerWidgetController = {
    run: function (params) {
        var datePicker = $('.datetimepicker');
        var dateFormat = datePicker.data('format');
        datePicker.datetimepicker({format: dateFormat});
    }
}