/**
 * Main Interactions
 */
/* global $ */

$(function() {

  // Auto-submit form on select input change
  $('.select-filter').on('change', function() {
    this.form.submit();
  });

  // Add/remove artist to favourites
  $('.action-fav-artist').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var artistID = parseInt(this.dataset.artistId, 10);
    $.post('api/fav-artist.php',
      {
        artistID: artistID,
        isFav: !$btn.hasClass('-active')
      },
      function(data) {
        $btn.text(data.isFav ? 'Favourited' : 'Add to Favourite')
          .toggleClass('-active', data.isFav)
          .blur();
        // TODO: Handle error
      }
    );
  });

  // Unmark or mark artwork as to see or have seen
  $('.action-mark-artwork').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var artworkID = parseInt(this.dataset.artworkId, 10);
    var status = this.dataset.status === 'To See' ? 'To See' : 'Have Seen';
    var isActive = $btn.hasClass('-active');
    $.post('api/mark-artwork.php',
      {
        artworkID: artworkID,
        status: isActive ? null : status
      },
      function(data) {
        $btn.toggleClass('-active', data.status === status)
          .blur()
          .siblings().removeClass('-active');
        // TODO: Handle error
      }
    );
  });

});
