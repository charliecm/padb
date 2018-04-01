/**
 * Main Interactions
 */
/* global $ */

$(function() {

  // Auto-submit form on select input change
  $('.select-filter').on('change', function() {
    this.form.submit();
  });

});
