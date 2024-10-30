<?php

# Load Twitter class
require_once('TwitterOAuth.php');

# Define constants
define('TWEET_LIMIT', 20);
define('TWITTER_USERNAME', 'narendramodi');
define('CONSUMER_KEY', 'S7o9Uvq8qOKUYdoVgALNGAAX9');
define('CONSUMER_SECRET', '49b33gVzC0VPH0tNvJfbXpFr85cyFIcru0ILw6XFmmjep7SHUh');
define('ACCESS_TOKEN', '1371363770-K85gCnGQYlg5MiWu2y5QRJoazoeezJhR6yC3SIr');
define('ACCESS_TOKEN_SECRET', 's6vGPNRepgeHMZGtSl8kVqftuQLGOlwfvvZ9sCvVkyKVB');

# Create the connection
$twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

# Migrate over to SSL/TLS
$twitter->ssl_verifypeer = true;

# Load the Tweets
$tweets = $twitter->get('statuses/user_timeline', array('screen_name' => TWITTER_USERNAME, 'exclude_replies' => 'true', 'include_rts' => 'false', 'count' => TWEET_LIMIT));

# Example output
if(!empty($tweets)) {
    foreach($tweets as $tweet) {

        # Access as an object
        $tweetText = $tweet['text'];

        # Make links active
        $tweetText = preg_replace("#(http://|(www\.))(([^\s<]{4,68})[^\s<]*)#", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $tweetText);

        # Linkify user mentions
        $tweetText = preg_replace("/@(w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $tweetText);

        # Linkify tags
        $tweetText = preg_replace("/#(w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>', $tweetText);

        # Output
        echo '<div>'.$tweetText.'</div>';

    }
}

?>