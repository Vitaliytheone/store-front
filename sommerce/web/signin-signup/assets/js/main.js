// Input type file;

var inputs = document.querySelectorAll(".hide-input");
Array.prototype.forEach.call(inputs, function(input) {
  var label = input.nextElementSibling,
    labelVal = label.innerHTML;
  input.addEventListener("change", function(e) {
    var fileName = "";
    if (this.files && this.files.length > 1)
      fileName = (this.getAttribute("data-multiple-caption") || "").replace(
        "{count}",
        this.files.length
      );
    else {
      var fullFileName = e.target.value.split("\\").pop();

      if (fullFileName != "") {
        var fileNameSplit = fullFileName.split(".");
        var firstName = fileNameSplit[0];
        var extension = fileNameSplit[1];

        if (firstName.length > 10) {
          firstName = firstName.substring(0, 10);
        }

        fileName = firstName + "." + extension;
      }
    }

    if (fileName) {
      label.querySelector("span").innerHTML = fileName;
      label.querySelector("span").classList.add("underline-text");
      label.querySelector("button").classList.add("new-privet");

      var button = label.querySelector("button");
      button.addEventListener("click", function(event) {
        document.getElementById("attach-file").value = null;
        label.innerHTML = labelVal;
        event.preventDefault();
      });
    } else label.innerHTML = labelVal;
  });
});
