<?php
// Include the Google API client library
require_once 'google-api-php-client/vendor/autoload.php';

// Set up the Google API client
$client = new Google_Client();
$client->setApplicationName('YouTube Channel Sync');
$client->setDeveloperKey('YOUR_API_KEY');

// Create a YouTube service object
$youtube = new Google_Service_YouTube($client);

// Define the YouTube channel IDs for testing
$channelIds = array(
'CHANNEL_ID_1',
'CHANNEL_ID_2',
'CHANNEL_ID_3'
);

// Connect to the database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "youtube_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Loop through each channel ID
foreach ($channelIds as $channelId) {
// Get the channel information
$channel = $youtube->channels->listChannels('snippet', array('id' => $channelId));
$channelInfo = $channel->getItems()[0]->getSnippet();
}
// Save the channel information in the database
$sql = "INSERT INTO youtube_channels (channel_id, name, description, profile_picture) VALUES ('$channelId', '$channelInfo->title', '$channelInfo->description', '$channelInfo->thumbnails->default->url')";
$conn->query($sql);

// Get the latest 100 videos from the channel
$videos = $youtube->search->listSearch('snippet', array('channelId' => $channelId, 'maxResults' => 100, 'order' => 'date'));

// Loop through each video and save it in the database
foreach ($videos->getItems() as $video) {
    $videoInfo = $video->getSnippet();
    $videoId = $video->getId()->getVideoId();

    $sql = "INSERT INTO youtube_channel_videos (channel_id, video_id, title, description, thumbnail) VALUES ('$channelId', '$videoId', '$videoInfo->title', '$videoInfo->description', '$videoInfo->thumbnails->default->url')";
    $conn->query($sql);
}


$conn->close();
?>