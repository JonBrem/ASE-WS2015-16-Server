<?php 
  /**
   * Opens a URL-connection in the background (by immediately terminating the connection after it is established).
   * Used for parallel / background behaviour in this application.
   * Copied from: <a href="http://stackoverflow.com/questions/962915/how-do-i-make-an-asynchronous-get-request-in-php">Stackoverflow, Question 962915</a>
   * @param string $type must equal 'GET' or 'POST'
   * @param array $params HTTP-Params for the request.
   * @param string $url url to call
   */
  function curl_request_async($url, $params, $type='POST') {
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
      }
      if(sizeof($params) == 0) $post_params = array();
      $post_string = implode('&', $post_params);

      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 0.2);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }