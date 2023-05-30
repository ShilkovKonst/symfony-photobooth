import $ from 'jquery'
import 'select2'

$(function() {
  $('.js-example-basic-single').select2({
    // allowClear: true,
    selectionCssClass: 'form-control'
  });
});