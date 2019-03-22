// Input type file;

var inputs = document.querySelectorAll('.hide');
Array.prototype.forEach.call(inputs, function (input) {
    var label = input.nextElementSibling,
        labelVal = label.innerHTML;
    input.addEventListener('change', function (e) {
        var fileName = '';
        if (this.files && this.files.length > 1)
            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
        else
            var a  = e.target.value.split('\\').pop();


        var split = a.split('.');
        console.log(split);
        var fileNames = split[0];
        var extension = split[1];
    

        if (fileNames.length > 10) {
            fileNames = fileNames.substring(0, 10);
        }

         fileName = fileNames + '.' + extension;

        if (fileName)
            label.querySelector('span').innerHTML = fileName;
        else
            label.innerHTML = labelVal;
    });
});