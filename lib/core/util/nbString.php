<?php

class nbString {
  public static function uncamelize($input, $separator = '-') {
    $matches = array();
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    
    $ret = $matches[0];
    
    foreach ($ret as &$match) {
      $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    
    return implode($separator, $ret);
  }
  
  public static function camelize($input, $separator = '-') {
    if($separator == '-')
      $separator = '\-';

    return preg_replace('/' . $separator. '(.)/e', "strtoupper('$1')", strtolower($input));
  }
  
  function capitalize($matches) {
    return strtoupper($matches[0][1]);
  }

}
