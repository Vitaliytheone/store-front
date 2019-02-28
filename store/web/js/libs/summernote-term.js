$(document).ready(function() {
  $('#termsofservice').summernote({
    minHeight: 200,
    toolbar: [
      ['style', ['bold', 'italic']],
      ['lists', ['ul', 'ol']],
      ['para', ['paragraph']],
      ['insert', ['link']],
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
    dialogsFade: true
  });
  $('#refundpolicy').summernote({
    minHeight: 200,
    toolbar: [
      ['style', ['bold', 'italic']],
      ['lists', ['ul', 'ol']],
      ['para', ['paragraph']],
      ['insert', ['link']],
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
    dialogsFade: true
  });
  $('#privacypolicy').summernote({
    minHeight: 200,
    toolbar: [
      ['style', ['bold', 'italic']],
      ['lists', ['ul', 'ol']],
      ['para', ['paragraph']],
      ['insert', ['link']],
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
    dialogsFade: true
  });
});