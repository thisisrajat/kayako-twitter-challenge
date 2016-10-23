<?php

  // require the class code
  require_once(dirname(__DIR__) . '/src/KayakoSearchWrapper.php');

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
  $requester = new App\KayakoSearchWrapper($params);

  // Grab the plain text response
  $response = $requester->response();

  // encode the response in json and then print it
  echo(json_encode($response));

?>