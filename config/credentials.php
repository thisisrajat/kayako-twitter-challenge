<?php
  /**
   * return an Array of tokens and env variables which are resolved at runtime
   */
  return array(
    'consumer_key' => getenv('CONSUMER_KEY'),
    'oauth_access_token' => getenv('OAUTH_TOKEN'),
    'consumer_secret' => getenv('CONSUMER_SECRET'),
    'oauth_access_token_secret' => getenv('OAUTH_SECRET')
  );
?>