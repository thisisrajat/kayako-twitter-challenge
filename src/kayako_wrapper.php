<?php

  // Defining a namespace
  namespace App\Twitter;

  // Autoloading all the vendor directory
  require_once("../vendor/autoload.php");

  /**
   * KayakoSearchWrapper: A small wrapper around Twitter API for searching tweets with specific hashtag and retweet count
   *
   * PHP version 5.4
   *
   * @category Api
   * @package  App\Twitter\KayakoSeachWrapper
   * @author   Rajat Jain <rajat.rj10@gmail.com>
   * @license  None
   * @version  0.1
   * @link     <no public pointing url>
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
    private static $credentials = array(
      'consumer_key' => "nXBMtCNRHaUIAjdDvG1wp2s24",
      'oauth_access_token' => "90846659-LfBOWfQsEETKqGlHygJdO3xhqLXNFZBTgHm96xPdR",
      'consumer_secret' => "mwlE5WBbXuf9W93QmmJPG4HOCpKptCTYM9kGGZ12WkQ2T9Cr5L",
      'oauth_access_token_secret' => "Ti4YxLRDr6cNtjtYuj0zDopeywpFJcS3fT4fM1V8pPI8o"
    );

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
      /**
       * Incoming retweetCount should be set else we'll fallback to 1 which is default val
       * It should be Integer
       * And should be equal or greater than 0
       */
      if(isset($params['retweetCount'])
        && is_numeric($params['retweetCount'])
        && intval($params['retweetCount']) >=0 ) {
        $this->retweetCount = intval($params['retweetCount']);
      }

      /**
       * Incoming query should be set else we'll fallback to default `#custserv`
       * Should have the first character as `#`, else prepend `#`
       * Html encode the query
       */
      if(isset($params['query'])) {
        $this->query = $params['query'];
        if($this->query[0] !== '#') {
          $this->query = '#' . $this->query;
        }
        $this->query = urlencode($this->query);
      }
    }

    /**
     * Generates the GET parameters for the API endpoint.
     * @return string
     */
    private function makeGetParameters() {
      if (!isset($this->query) || $this->query === '') {
        exit('{}');
      }
      $slug = '?result_type=popular&q=' . $this->query;
      return $slug;
    }

    /**
     * Logic for filtering tweets based on retweetCount
     * @param  array $tweet A single Tweet object in json format
     * @return bool (true || false)
     */
    private function filterTweets($tweet) {
      if (isset($tweet['retweet_count']) && is_int($this->retweetCount)) {
        $temp_count = intval($tweet['retweet_count']);
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
     * Returns the filtered response by querying Twitter's Search API endpoint
     * @return json
     */
    public function response() {
      $slug = $this->makeGetParameters();
      $proxy = new \TwitterAPIExchange(KayakoSearchWrapper::$credentials);
      $response = $proxy->request(KayakoSearchWrapper::URL, KayakoSearchWrapper::METHOD, $slug);
      $response = json_decode($response, true);
      if (isset($response['statuses'])) {
        return array_filter($response['statuses'], array($this, 'filterTweets'));
      }
      else {
        exit('{"error": "Something went wrong."}');
      }
    }
  }

  /**
   * Create the barebone paramters for Twitter API
   * @var array
   */
  $params = array(
    "retweetCount" => $_GET['retweetCount'],
    "query" => urldecode($_GET['query'])
  );

  /**
   * Requester is the absraction of Twitter's API and instance of class KayakoSearchWrapper
   * @var KayakoSearchWrapper
   */
  $requester = new KayakoSearchWrapper($params);

  // Grab the plain text response
  $response = $requester->response();

  // encode the response in json and then print it
  echo(json_encode($response));

?>