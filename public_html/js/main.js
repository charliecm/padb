/**
 * Main Interactions
 */
/* global $, google */

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

  // Add accordion interaction
  $('.accordion__header').on('click', function(event) {
    event.preventDefault();
    var $header = $(this);
    var isActive = !$header.parent().next('.accordion__body').toggleClass('-hidden').hasClass('-hidden');
    var id = this.dataset.id;
    var post = {};
    post[id] = isActive;
    $header.toggleClass('-active', isActive);
    $.post('api/update-prefs.php', post);
  });

});

// Initializes map view for artworks page
function initMap() { // eslint-disable-line
  var $map = $('#map');
  if (!$map.length || !window.mapResults) return;

  var MIN_WIDTH = 768;
  var $window = $(window);
  var isInit = false;

  function init() {
    // Setup map
    var results = window.mapResults;
    var bounds = new google.maps.LatLngBounds();
    var infowindow = new google.maps.InfoWindow();
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 4
    });

    results.forEach(function(item) {
      // Populate map markers
      var marker = new google.maps.Marker({
        position: item,
        icon: 'http://maps.google.com/mapfiles/marker' + item.letter + '.png',
        map: map
      });
      marker.addListener('click', function() {
        // Show item details
        infowindow.setContent('<div class="infowindow"><p class="no-drop"><a href="' + item.url + '"><small><strong>' + item.title + '</strong></small></a></p><img src="' + item.photoURL + '"></div>');
        infowindow.open(map, marker);
      });
      bounds.extend(marker.getPosition());
    });
    map.fitBounds(bounds);

    // Add zoom to marker interaction for list item map marker button
    $('.action-map-center').on('click', function() {
      var i = this.dataset.index;
      infowindow.close();
      map.setCenter(results[i]);
      map.setZoom(17);
    });
  }

  // Load map when window is large enough
  $window.on('resize', function() {
    if ($window.width() >= MIN_WIDTH && !isInit) {
      init();
      $window.off('resize');
      isInit = true;
    }
  });
  $window.trigger('resize');
}
