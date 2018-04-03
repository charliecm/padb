/**
 * Main Interactions
 */
/* global $ */

$(function() {

  // Auto-submit form on select input change
  $('.select-filter').on('change', function() {
    this.form.submit();
  });

  // Add/remove artist from favourites
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

  // Remove artist from favourites
  $('.action-unfav-artist').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var artistID = parseInt(this.dataset.artistId, 10);
    var artistName = this.dataset.artistName;
    if (!confirm('Are you sure you want to remove "' + artistName + '" from favourites?')) { // eslint-disable-line
      $btn.blur();
      return;
    }
    $.post('api/fav-artist.php',
      {
        artistID: artistID,
        isFav: false
      },
      function() {
        var $list = $btn.parents('.list');
        if ($list.children().length === 1) {
          // Show empty state copy
          $list.prev('.empty-copy').removeClass('-hidden');
        }
        $btn.off('click')
          .parent().remove();
        // TODO: Handle error
      }
    );
  });

  // Unmark or mark artwork as to see or seen
  $('.action-mark-artwork').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var artworkID = parseInt(this.dataset.artworkId, 10);
    var status = this.dataset.status === 'To See' ? 'To See' : 'Seen';
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

  // Unmarks an artwork
  $('.action-unmark-artwork').on('click', function(event) {
    event.preventDefault();
    var $btn = $(this);
    var artworkID = parseInt(this.dataset.artworkId, 10);
    var artworkTitle = this.dataset.artworkTitle;
    if (!confirm('Are you sure you want to remove "' + artworkTitle + '"?')) { // eslint-disable-line
      $btn.blur();
      return;
    }
    $.post('api/mark-artwork.php',
      {
        artworkID: artworkID,
        status: null
      },
      function() {
        var $list = $btn.parents('.list');
        if ($list.children().length === 1) {
          // Show empty state copy
          $list.prev('.empty-copy').removeClass('-hidden');
        }
        $btn.off('click')
          .parent().remove();
        // TODO: Handle error
      }
    );
  });

});
