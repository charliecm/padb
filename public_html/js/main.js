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
      }
    );
  });
      }
    );
  });

});
