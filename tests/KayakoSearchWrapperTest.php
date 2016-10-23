<?php

  // Defining a namespace and a little housekeeping
  namespace App;

  use PHPUnit\Framework\TestCase;

  require_once(dirname(__DIR__) . "/src/KayakoSearchWrapper.php");

  /**
   *  Unit Tests for KayakoSearchWrapper
   */
  class KayakoSearchWrapperTest extends \PHPUnit_Framework_TestCase {
    /**
     * When query is empty
     */
    public function testQueryEmpty() {
      $params = array(
        "query" => '',
        "retweetCount" => 1
      );
      $requester = new KayakoSearchWrapper($params);
      $this->assertEquals("%23custserv", $requester->getQuery());
    }

    /**
     * When Query is not set at all
     */
    public function testQueryNotSet() {
      $params = array(
        "retweetCount" => 1
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertEquals("%23custserv", $req->getQuery());
    }

    /**
     * Retweet Count is not set
     */
    public function testRetweetCountNotSet() {
      $params = array(
        "query" => "#custserv"
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertEquals(1, $req->getRetweetCount());
    }

    /**
     * When retweetCount passed as string but is numeric
     */
    public function testRetweetCountPassedAsString() {
      $params = array (
        "query" => "#custserv",
        "retweetCount" => "2"
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertEquals("2", $req->getRetweetCount());
      $this->assertTrue(is_int($req->getRetweetCount()));
    }

    /**
     * When retweetCount is string and is non numeric
     */
    public function testRetweetCountNonNumeric() {
      $params = array (
        "query" => "#custserv",
        "retweetCount" => "blabla"
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertEquals(1, $req->getRetweetCount());
    }

    /**
     * Check if we are encoding the get parameters properly
     */
    public function testMakeGetParameters() {
      $params = array (
        "query" => "#custserv",
        "retweetCount" => 1
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertEquals("?result_type=popular&q=%23custserv", $req->makeGetParameters());
    }

    /**
     *  Filter should return when the count of retweet_count is higher than what
     *  we expect
     */
    public function testFilterTweetsTrue() {
      $params = array (
        "query" => "#custserv",
        "retweetCount" => 2
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertTrue($req->filterTweets(array("retweet_count" => 4)));
      $this->assertTrue($req->filterTweets(array("retweet_count" => 10)));
      $this->assertTrue($req->filterTweets(array("retweet_count" => "1231")));
    }

    /**
     * Filter should not return any of the tweet which have retweet_count less
     * than what we are expecting
     */
    public function testFilterTweetsFalse() {
      $params = array (
        "query" => "#custserv",
        "retweetCount" => 3
      );
      $req = new KayakoSearchWrapper($params);
      $this->assertFalse($req->filterTweets(array("retweet_count" => 1)));
      $this->assertFalse($req->filterTweets(array("retweet_count" => 2)));
      $this->assertFalse($req->filterTweets(array("retweet_count" => "1")));
    }

  }

?>