import '../styles/select2.css'
import $ from 'jquery'
import 'select2'

$(function() {
  $('.js-example-basic-single').select2({
    // allowClear: true,
    selectionCssClass: 'form-control',
    width: 'resolve'
  });
});