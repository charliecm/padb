/**
 * Main Interactions
 */
/* global $ */

$(function() {

  // Auto-submit form on select input change
  $('.select-filter').on('change', function() {
    this.form.submit();
  });

  $('.action-add-to-fav').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var id = parseInt(this.dataset.id, 10);
    $.post('api/fav-artist.php',
      {
        artistID: id,
        isFav: !$btn.hasClass('-active')
      },
      function(data) {
        console.log('success', data);
        $btn.text(data.isFav ? 'Favourited' : 'Add to Favourite');
        $btn.toggleClass('-active', data.isFav);
        $btn.blur();
      }
    );
  });

});
