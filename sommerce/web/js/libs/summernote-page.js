$(document).ready(function() {
  $('#content').summernote({
    minHeight: 300,
    focus: true,
    toolbar: [
      ['style', ['style', 'bold', 'italic']],
      ['lists', ['ul', 'ol']],
      ['para', ['paragraph']],
      ['color', ['color']],
      ['insert', ['link', 'picture', 'video']],
      ['codeview', ['codeview']]
    ],
    disableDragAndDrop: true,
    styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    popover: {
      image: [
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ],
    },
    dialogsFade: true,
  });
});