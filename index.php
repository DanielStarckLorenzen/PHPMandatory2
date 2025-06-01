<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration and required files
require_once 'config/config.php';
require_once 'src/utils/Router.php';
require_once 'src/utils/Logger.php';
require_once 'src/db/Database.php';
require_once 'src/models/Artist.php';
require_once 'src/models/Album.php';
require_once 'src/models/Track.php';
require_once 'src/models/Playlist.php';
require_once 'src/models/MediaType.php';
require_once 'src/models/Genre.php';
require_once 'src/controllers/BaseController.php';
require_once 'src/controllers/ArtistController.php';
require_once 'src/controllers/AlbumController.php';
require_once 'src/controllers/TrackController.php';
require_once 'src/controllers/PlaylistController.php';
require_once 'src/controllers/GenreController.php';
require_once 'src/controllers/MediaTypeController.php';

use Chinook\Utils\Logger;
use Chinook\Utils\Router;

// Initialize logger
$logger = new Logger();
$logger->log($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);

// Initialize router
$router = new Router();

// Register routes for artists
$router->get('/artists', 'ArtistController@getAll');
$router->get('/artists/(\d+)', 'ArtistController@getOne');
$router->get('/artists/(\d+)/albums', 'ArtistController@getAlbums');
$router->post('/artists', 'ArtistController@create');
$router->delete('/artists/(\d+)', 'ArtistController@delete');

// Register routes for albums
$router->get('/albums', 'AlbumController@getAll');
$router->get('/albums/(\d+)', 'AlbumController@getOne');
$router->get('/albums/(\d+)/tracks', 'AlbumController@getTracks');
$router->post('/albums', 'AlbumController@create');
$router->put('/albums/(\d+)', 'AlbumController@update');
$router->delete('/albums/(\d+)', 'AlbumController@delete');

// Register routes for tracks
$router->get('/tracks', 'TrackController@search');
$router->get('/tracks/(\d+)', 'TrackController@getOne');
$router->post('/tracks', 'TrackController@create');
$router->put('/tracks/(\d+)', 'TrackController@update');
$router->delete('/tracks/(\d+)', 'TrackController@delete');

// Register routes for media types and genres
$router->get('/media_types', 'MediaTypeController@getAll');
$router->get('/genres', 'GenreController@getAll');

// Register routes for playlists
$router->get('/playlists', 'PlaylistController@getAll');
$router->get('/playlists/(\d+)', 'PlaylistController@getOne');
$router->post('/playlists', 'PlaylistController@create');
$router->post('/playlists/(\d+)/tracks', 'PlaylistController@addTrack');
$router->delete('/playlists/(\d+)/tracks/(\d+)', 'PlaylistController@removeTrack');
$router->delete('/playlists/(\d+)', 'PlaylistController@delete');

// Handle the request
$router->handleRequest(); 