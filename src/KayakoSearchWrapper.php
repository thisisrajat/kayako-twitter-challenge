<?php
  // Defining a namespace
  namespace App;

  // Autoloading all the vendor directory
  require_once(dirname(__DIR__) . "/vendor/autoload.php");

  /**
   * KayakoSearchWrapper: A small wrapper around Twitter API for searching tweets with specific hashtag and retweet count
   *
   * PHP version 5.4
   *
   * @category Api
   * @package  App\KayakoSeachWrapper
   * @author   Rajat Jain <rajat.rj10@gmail.com>
   * @license  None
   * @version  0.1
   * @link     http://kayako.rajatja.in
   */
  class KayakoSearchWrapper {
    /**
     * The Twitter endpoint we'll be requesting for searching.
     * @var string
     */
    const URL = 'https://api.twitter.com/1.1/search/tweets.json';
    /**
     * HTTP method via which we'll be requesting to the Twitter search API
     *  @var string
     */
    const METHOD = 'GET';

    /**
     * Oauth and Consumer tokens which are essential for requesting to the Twitter API.
     * Don't touch them unless you know what you're doing.
     * @var array
     */
    private $credentials;

    /**
     * Number of retweets that are required in the API search result
     * @var int
     */
    private $retweetCount = 1;

    /**
     * The value that to be queried to the Twitter endpoint
     * @var string
     */
    private $query = "#custserv";

    /**
     * Parameterized constructor for this class
     * @param array $params
     */
    public function __construct(array $params) {

      // Initialize credentials by fetching from env variables
      $this->credentials = array(
        'consumer_key' => getenv('CONSUMER_KEY'),
        'oauth_access_token' => getenv('OAUTH_TOKEN'),
        'consumer_secret' => getenv('CONSUMER_SECRET'),
        'oauth_access_token_secret' => getenv('OAUTH_SECRET')
      );

      /**
       * Incoming retweetCount should be set else we'll fallback to 1 which is default val
       * It should be Integer
       * And should be equal or greater than 0
       */
      if(isset($params['retweetCount'])
        && is_numeric($params['retweetCount'])
        && intval($params['retweetCount']) >= 0 ) {
        $this->retweetCount = intval($params['retweetCount']);
      }

      /**
       * Incoming query should be set else we'll fallback to default `#custserv`
       * Should have the first character as `#`, else prepend `#`
       */
      if(isset($params['query'])) {
        if($params['query']=== '') {
          $params['query'] = "custserv";
        }
        $this->query = $params['query'];
        if($this->query[0] !== '#') {
          $this->query = '#' . $this->query;
        }
      }
      // HTML encode the query
      $this->query = urlencode($this->query);
    }

    /**
     * Generates the GET parameters for the API endpoint.
     * @return string
     */
    public function makeGetParameters() {
      // query should always be set and not be empty string
      // if not, then something is really really wrong!
      if (!isset($this->query) || $this->query === '') {
        exit('{}');
      }
      // Form the slug for the api url
      $slug = '?result_type=popular&q=' . $this->query;
      return $slug;
    }

    /**
     * Logic for filtering tweets based on retweetCount
     * @param  array $tweet A single Tweet obj
     * @return bool (true || false)
     */
    public function filterTweets($tweet) {
      /**
       * In the tweet object we should have retweet_count set and,
       * The instance variable we already have should be an integer
       * Always do INT arithmetic!
       */
      if (isset($tweet['retweet_count']) && is_int($this->retweetCount)) {
        // convert retweet_count into integer
        $temp_count = intval($tweet['retweet_count']);
        // Filter the tweets
        if($temp_count >= $this->retweetCount) {
          return true;
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }

    /**
     * returns the query instance variable
     * @return string
     */
    public function getQuery() {
      return $this->query;
    }

    /**
     * returns the retweetCount instance variable
     * @return int
     */
    public function getRetweetCount() {
      return $this->retweetCount;
    }

    /**
     * Returns the filtered response by querying Twitter's Search API endpoint
     * @return json
     */
    public function response() {
      // build the slug by query parameter
      $slug = $this->makeGetParameters();

      // instantiate TwitterAPIExchange which will do Oauth on our behalf
      $proxy = new \TwitterAPIExchange($this->credentials);

      // Passing API Endpoint, HTTP Method and slug
      $response = $proxy->request(KayakoSearchWrapper::URL, KayakoSearchWrapper::METHOD, $slug);

      // We got a json response, and decoding it
      $response = json_decode($response, true);

      // If the statuses exists, else we messed something up
      if (isset($response['statuses'])) {
        // Filter all the tweets that satisfy our condiition
        return array_filter($response['statuses'], array($this, 'filterTweets'));
      }
      else {
        exit('{"error": "Something went wrong."}');
      }
    }
  }
?>