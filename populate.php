<?php
/**
 * Populate Database
 */

require('private/functions.php');

const PATH_ARTIST = 'artist.csv';
const PATH_ARTWORKS = 'public_art.csv';

$artist_ids = array(); // For mapping dataset ARTISTID to artistID
$db = db_connect();

/**
 * Parses artists dataset and populate the database.
 */
function parseArtists() {
	global $db, $artist_ids;
	$country_ids = array();

	// Open artist dataset
	$handle = fopen(PATH_ARTIST, 'r');
	$row = fgetcsv($handle);
	$first_line = TRUE;
	$count = 0;
	$total = 0;

	echo '<h1>Artists</h1>';
	while ($row = fgetcsv($handle)) {
		if ($first_line) { $first_line = FALSE; continue; }
		$total++;
		$csv = implode($row, ', ');
		$id = $row[0];
		$firstName = empty($row[1]) ? NULL : $row[1];
		$lastName = empty($row[2]) ? NULL : $row[2];
		$country = empty($row[3]) ? NULL : $row[3];
		$websiteURL = empty($row[4]) ? NULL : $row[4];
		$biography = empty($row[5]) ? NULL : $row[5];
		$photoURL = empty($row[6]) ? NULL : $row[6];
		$biographyURL = empty($row[8]) ? NULL : $row[8];
		$countryID = NULL;

		// Validate required fields
		if (empty($lastName)) {
			echo "<p><strong>Missing last name.</strong><br><span>$csv</span></p>";
			continue;
		}

		// Check country
		if (!empty($country)) {
			$index = array_search($country, $country_ids);
			if (array_key_exists($country, $country_ids)) {
				// Set countryID
				$countryID = $country_ids[$country];
			} else {
				// Insert new country
				$query = "INSERT INTO countries SET name = ?";
				if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
				$stmt->bind_param('s', $country);
				$stmt->execute();
				if ($stmt->affected_rows <= 0) {
					$error = $stmt->error;
					echo "<p><strong>Failed to add country '$country'</strong>: $error</p>";
				} else {
					// Set countryID to newly created country instance
					$countryID = $db->insert_id;
					$country_ids[$country] = $countryID;
					echo "<p>Added country '$country'.</p>";
				}
				$stmt->free_result();
			}
		}

		// Insert new artist
		$query = "INSERT INTO artists (firstName, lastName, websiteURL, biography, biographyURL, photoURL, countryID) VALUES (?,?,?,?,?,?,?)";
		if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
		$stmt->bind_param('ssssssi', $firstName, $lastName, $websiteURL, $biography, $biographyURL, $photoURL, $countryID);
		$stmt->execute();
		if ($stmt->affected_rows <= 0) {
			$error = $stmt->error;
			echo "<p><strong>Failed to add artist:</strong> $error<br><span>$csv</span></p>";
		} else {
			// Map ARTISTID to artistID
			$artist_ids[$id] = $db->insert_id;
			$count++;
		}
		$stmt->free_result();
	}

	echo "<p>$count of $total rows were imported.</p>";
	echo '</body></html>';
	fclose($handle);
}

/**
 * Parses artworks dataset and populate the database.
 */
function parseArtworks() {
	global $db, $artist_ids;
	$neighborhood_ids = array();
	$owner_ids = array();
	$type_ids = array();

	// Open artist dataset
	$handle = fopen(PATH_ARTWORKS, 'r');
	$row = fgetcsv($handle);
	$first_line = TRUE;
	$count = 0;
	$total = 0;

	echo '<h1>Artworks</h1>';
	while ($row = fgetcsv($handle)) {
		if ($first_line) { $first_line = FALSE; continue; }
		$total++;
		$csv = implode($row, ', ');
		$id = 0;
		$title = empty($row[1]) ? NULL : $row[1];
		$yearInstalled = empty($row[2]) ? NULL : ($row[2] . '-01-01');
		$status = ($row[3] === 'In place') ? 'In Place' : 'Removed';
		$description = empty($row[4]) ? NULL : $row[4];
		$statement = empty($row[5]) ? NULL : $row[5];
		$siteName = empty($row[6]) ? NULL : $row[6];
		$siteAddress = empty($row[7]) ? NULL : $row[7];
		$neighborhood = empty($row[8]) ? 'Unknown' : $row[8];
		$latitude = empty($row[9]) ? NULL : $row[9];
		$longitude = empty($row[10]) ? NULL : $row[10];
		$type = empty($row[12]) ? 'Other' : $row[12];
		$material = empty($row[13]) ? NULL : ucfirst($row[13]);
		$owner = empty($row[14]) ? 'Unknown' : ucfirst($row[14]);
		$websiteURL = empty($row[15]) ? NULL : $row[15];
		$photoURL = empty($row[16]) ? NULL : $row[16];
		$artists = empty($row[18]) ? NULL : explode(';', $row[18]);
		$has_invalid_artist = FALSE;
		$neighborhoodID = NULL;
		$ownershipID = NULL;
		$typeID = NULL;

		// Validate required fields
		if (empty($title)) {
			echo "<p><strong>Missing title.</strong><br><span>$csv</span></p>";
			continue;
		}
		if (empty($yearInstalled)) {
			echo "<p><strong>Missing year installed.</strong><br><span>$csv</span></p>";
			continue;
		}
		if (empty($siteName) && empty($siteAddress) && empty($latitude) && empty($longitude)) {
			// Ignore works without any location data
			echo "<p><strong>Missing site address and GPS coordinates.</strong><br><span>$csv</span></p>";
			continue;
		}
		if (empty($artists)) {
			echo "<p><strong>Missing artists.</strong><br><span>$csv</span></p>";
			continue;
		} else {
			// Check if artist id exists in artists table
			foreach ($artists as $artist) {
				if (!array_key_exists($artist, $artist_ids)) {
					$has_invalid_artist = TRUE;
					break;
				}
			}
			if ($has_invalid_artist) {
				echo "<p><strong>Artwork has invalid artists.</strong><br><span>$csv</span></p>";
				continue;
			}
		}

		// Check neighborhood
		$index = array_search($neighborhood, $neighborhood_ids);
		if (array_key_exists($neighborhood, $neighborhood_ids)) {
			// Set neighborhoodID
			$neighborhoodID = $neighborhood_ids[$neighborhood];
		} else {
			// Insert new neighborhood
			$query = "INSERT INTO neighborhoods SET name = ?";
			if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
			$stmt->bind_param('s', $neighborhood);
			$stmt->execute();
			if ($stmt->affected_rows <= 0) {
				$error = $stmt->error;
				echo "<p><strong>Failed to add neighborhood '$neighborhood'</strong>: $error</p>";
			} else {
				// Set neighborhoodID to newly created neighborhood instance
				$neighborhoodID = $db->insert_id;
				$neighborhood_ids[$neighborhood] = $neighborhoodID;
				echo "<p>Added neighborhood '$neighborhood'.</p>";
			}
			$stmt->free_result();
		}

		// Check owner
		if ($owner === 'Prov. of B.C.') {
			// Fix BC owner name
			$owner = 'Province of British Columbia';
		}
		$index = array_search($owner, $owner_ids);
		if (array_key_exists($owner, $owner_ids)) {
			// Set ownerID
			$ownerID = $owner_ids[$owner];
		} else {
			// Insert new owner
			$query = "INSERT INTO owners SET name = ?";
			if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
			$stmt->bind_param('s', $owner);
			$stmt->execute();
			if ($stmt->affected_rows <= 0) {
				$error = $stmt->error;
				echo "<p><strong>Failed to add owner '$owner'</strong>: $error</p>";
			} else {
				// Set ownerID to newly created owner instance
				$ownerID = $db->insert_id;
				$owner_ids[$owner] = $ownerID;
				echo "<p>Added owner '$owner'.</p>";
			}
			$stmt->free_result();
		}

		// Check type
		$index = array_search($type, $type_ids);
		if (array_key_exists($type, $type_ids)) {
			// Set typeID
			$typeID = $type_ids[$type];
		} else {
			// Insert new type
			$query = "INSERT INTO types SET type = ?";
			if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
			$stmt->bind_param('s', $type);
			$stmt->execute();
			if ($stmt->affected_rows <= 0) {
				$error = $stmt->error;
				echo "<p><strong>Failed to add type '$type'</strong>: $error</p>";
			} else {
				// Set typeID to newly created type instance
				$typeID = $db->insert_id;
				$type_ids[$type] = $typeID;
				echo "<p>Added type '$type'.</p>";
			}
			$stmt->free_result();
		}

		// Insert new artwork
		$query = "INSERT INTO artworks (title, status, yearInstalled, siteName, siteAddress, description, statement, latitude, longitude, material, photoURL, websiteURL, neighborhoodID, ownerID, typeID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
		$stmt->bind_param('sssssssddsssiii', $title, $status, $yearInstalled, $siteName, $siteAddress, $description, $statement, $latitude, $longitude, $material, $photoURL, $websiteURL, $neighborhoodID, $ownerID, $typeID);
		$stmt->execute();
		if ($stmt->affected_rows <= 0) {
			$error = $stmt->error;
			echo "<p><strong>Failed to add artwork:</strong> $error<br><span>$csv</span></p>";
		} else {
			$id = $db->insert_id;
			$count++;
		}
		$stmt->free_result();

		// Create artist/artwork relationship
		if (!$id) continue;
		foreach ($artists as $artist) {
			$artist_id = $artist_ids[$artist];
			// Insert artist/artwork relationship
			$query = "INSERT INTO artistArtworks (artistId, artworkID) VALUES (?,?)";
			if (!$stmt = $db->prepare($query)) echo "<p><strong>Statement failed:</strong> $db->error</p>";
			$stmt->bind_param('ii', $artist_id, $id);
			$stmt->execute();
			if ($stmt->affected_rows <= 0) {
				$error = $stmt->error;
				echo "<p><strong>Failed to associate artwork with artist ($artist_id):</strong> $error<br><span>$csv</span></p>";
			}
			$stmt->free_result();
		}
	}

	echo "<p>$count of $total rows were imported.</p>";
	echo '</body></html>';
	fclose($handle);
}

// Start script and output results
ob_start();
echo '<!doctype html><html lang="en"><head><title>Populate</title><style>
	body{ font-family: Roboto Mono, Menlo, Courier New, monospace; font-size: 0.75rem; line-height: 1.5 }
	p { margin-top:0; margin-bottom:0; padding-bottom:1.5rem }
	p > span{ display:none; opacity:0.5 }
	p:hover span{ display:inline-block }
	</style></head><body>';
parseArtists();
parseArtworks();
?>
