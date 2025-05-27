# Chinook REST API

A RESTful API for the Chinook database built with PHP 8.

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled

## Setup Instructions

1. Clone this repository to your web server's document root or a subdirectory.
2. Import the Chinook database into your MySQL server.
   - You can download the Chinook database from [here](https://github.com/lerocha/chinook-database).
   - Use the "ChinookDatabase/DataSources/Chinook_MySql_AutoIncrementPKs.sql" script.
3. Configure the database connection in `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'chinook');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
4. Make sure Apache's mod_rewrite is enabled and `.htaccess` files are allowed.
5. Ensure the `logs` directory is writable by the web server.

## API Endpoints

### Artists
- `GET /artists` - Get all artists
- `GET /artists?s=<search_text>` - Search artists by name
- `GET /artists/<artist_id>` - Get a specific artist
- `GET /artists/<artist_id>/albums` - Get all albums by an artist
- `POST /artists` - Create a new artist (requires `name` parameter)
- `DELETE /artists/<artist_id>` - Delete an artist (only if it has no albums)

### Albums
- `GET /albums` - Get all albums with their artists
- `GET /albums?s=<search_text>` - Search albums by title
- `GET /albums/<album_id>` - Get a specific album with its artist
- `GET /albums/<album_id>/tracks` - Get all tracks in an album
- `POST /albums` - Create a new album (requires `title` and `artist_id` parameters)
- `PUT /albums/<album_id>` - Update an album (accepts `title` and `artist_id` parameters)
- `DELETE /albums/<album_id>` - Delete an album (only if it has no tracks)

### Tracks
- `GET /tracks?s=<search_text>` - Search tracks by name
- `GET /tracks/<track_id>` - Get a specific track
- `GET /tracks?composer=<composer>` - Get tracks by a specific composer
- `POST /tracks` - Create a new track (requires multiple parameters)
- `PUT /tracks/<track_id>` - Update a track
- `DELETE /tracks/<track_id>` - Delete a track (only if it's not in any playlist)

### Media Types and Genres
- `GET /media_types` - Get all media types
- `GET /genres` - Get all genres

### Playlists
- `GET /playlists` - Get all playlists
- `GET /playlists?s=<search_text>` - Search playlists by name
- `GET /playlists/<playlist_id>` - Get a specific playlist with its tracks
- `POST /playlists` - Create a new playlist (requires `name` parameter)
- `POST /playlists/<playlist_id>/tracks` - Add a track to a playlist (requires `track_id` parameter)
- `DELETE /playlists/<playlist_id>/tracks/<track_id>` - Remove a track from a playlist
- `DELETE /playlists/<playlist_id>` - Delete a playlist (only if it has no tracks)

## Security Features

- Protection against SQL injection using prepared statements
- XSS prevention through input sanitization
- Proper error handling and HTTP status codes
- Request logging for audit purposes 