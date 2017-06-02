<?php
//error_reporting(E_NOTICE); // You may change this for debugging purposes if you'd like.
require_once('Twitter.php'); // Twitter Class by Tijs Verkoyen
use \TijsVerkoyen\Twitter\Twitter;

/*
	JSON Feed to Twitter using PHP
	By: Colin Devroe
	http://cdevroe.com/
	http://github.com/cdevroe
	https://github.com/cdevroe/jsonfeed2twitter

  RSS to Twitter was originally written on December 6, 2009 while watching
	Star Trek III: The Search for Spock.

  JSON Feed to Twitter was written in a matter of minutes on a
  beautiful sunny day at my desk at work in Scranton, PA.

	Version 0.1 - June 2, 2017
  See README for installation instructions, licensing, version history, etc.
*/

/* Configuration */
$jsonfeed2twitter              = new Twitter( '', '' ); // consumerKey, consumerSecret
$jsonfeed2twitter              ->setOAuthToken(''); // OAuthToken
$jsonfeed2twitter              ->setOAuthTokenSecret(''); // OAuthTokenSecret

// Feed URL and cache directory
$feed_url                       = 'http://cdevroe.com/feed/json'; // e.g. http://cdevroe.com/feed/json
$cache_directory                = "cache/"; // e.g. /home/.eastwood/domain.com/directory/' Leave blank to turn off caching

/*
  Tweet Message format (link is automatically appended)
  - %TITLE% (post title)
  - %BODY% (post body)
  - %URL% (post URL)

  Examples:
    - '%TITLE% - %URL%'
    - '%BODY%' - Good for statuses (or microblog posts, or titlelessposts)
*/
$tweet_format                   = '"%TITLE%" - %URL%';

/*
  Age of posts to tweet

  If  you're adding a JSON Feed with hundreds of items and you don't want them all tweeted.
  You can set this to only tweet posts that have been published within the last
  X hours. I recommend 12 hours.
*/
$posts_less_than_X_hours_old    = 12;

/* End Configuration */


/*
  Create Tweet based on tweet format config
  Accepts: $post array
  Returns: tweet string
*/
function create_tweet($post) {
	global $tweet_format;

	// Construct Tweet Format
	$message                     = str_replace("%TITLE%", $post->title, $tweet_format);
	$message                     = str_replace("%BODY%", strip_tags($post->content_html), $message);

	// Shorten the tweet if longer than 140 chars incl. 23 char t.co link
	if (strlen($message) > 116) {
		$message                  = substr($message, 0, 113).'...';
	}

  // Add URL
  $message                    = str_replace("%URL%", $post->url, $message);

	return $message;
}

function init() {
  global $jsonfeed2twitter,
         $feed_url,
         $cache_directory,
         $posts_less_than_X_hours_old;

  // Retrieve and load cached tweets
  if ( $cache_directory != '' ) {
    $cache_file               = $cache_directory . "tweets.txt";
    if ( $cached_tweets = @file_get_contents($cache_file) ) { $cached_tweets = unserialize($cached_tweets); }
    if ( !$cached_tweets || !is_array($cached_tweets) ) { $cached_tweets = array_fill(0, 19, "-"); }
  }

  // Retrieve and parse JSON Feed
  $curl                       = curl_init($feed_url);
  	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  	$json                     = json_decode( curl_exec($curl) );
  curl_close($curl);

  foreach( $json->items as $post ) :
    $date_published           = ($post->date_published != NULL) ? strtotime($post->date_published) : $post->date_published;

    /*
      Post to Twitter if:
        - The url has never been tweeted.
        - The post is less than (timeout) hours old.
    */
    if ( in_array($post->url, $cached_tweets) === false && ( $date_published == NULL || $date_published > time() - (60 * 60 * $posts_less_than_X_hours_old) ) ) {

      // Create the tweet based on this post and send to Twitter
  		$tweet                  = $jsonfeed2twitter->statusesUpdate( create_tweet($post) );

  		print_r($tweet);

  		// If success write to cache, else fail
  		if ( isset($tweet['id']) ) {
  			$cached_tweets[]      = $post->url;
  			if ( count($cached_tweets) > 50 ) { array_shift($cached_tweets); }
  		} else {
  			print "\n" . 'There was an error. Tweet not sent for this post. -> ' . $post->url . "\n";
  		}

  	} // end if (post to twitter)

  endforeach;
}

init();
