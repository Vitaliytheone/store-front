var customModule = {};
window.modules = {};

$(function() {
    if ('object' == typeof window.modules) {
        $.each(window.modules, function(name, options) {
            if ('undefined' != typeof customModule[name]) {
                customModule[name].run(options);
            }
        });
    }
});