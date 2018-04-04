# PADb

PADb is a website for people to discover public artworks around the city of Vancouver. They can browse artworks on a map and filter by attributes like neighborhood, artist and type of work. Visitors can register for an account to keep track of art they'd like to see or have seen, and add artists as their favourites.

PADb uses the [public art dataset](http://data.vancouver.ca/datacatalogue/publicArt.htm) from the City of Vancouver's Open Data Catalogue.

This project is created by Charlie Chao for IAT352: Internet Computing Technologies. It's meant to run on a XAMPP server with Apache and MariaDB.

## Development

To setup database, run `padb.sql` and copy `private/config.default.php` as `private/config.php` with the correct database credential and any other API keys.

For building the front-end, please install [node](https://nodejs.org) on your system. Start SASS watch and livereload by running:

    npm run watch
