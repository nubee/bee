<?php

class nbAnsiColorFormatter extends nbFormatter
{
  protected
    static $styles = array(
      'error'    => array('bg' => 'red', 'fg' => 'white', 'bold' => true),
      'info'     => array('fg' => 'green', 'bold' => true),
      'comment'  => array('fg' => 'yellow'),
      'question' => array('bg' => 'cyan', 'fg' => 'black', 'bold' => false),
    ),
    $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8),
    $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37),
    $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

  protected function replaceStartStyle($match)
  {
    if (!isset(self::$styles[strtolower($match[1])]))
      throw new InvalidArgumentException(sprintf('[nbAnsiColorFormatter::replaceStartStyle] Unknown style "%s".', $match[1]));

    $parameters = static::$styles[strtolower($match[1])];
    $codes = array();

    if (isset($parameters['fg']))
      $codes[] = static::$foreground[$parameters['fg']];

    if (isset($parameters['bg']))
      $codes[] = static::$background[$parameters['bg']];

    foreach (static::$options as $option => $value) {
      if (isset($parameters[$option]) && $parameters[$option])
        $codes[] = $value;
    }

    return "\033[".implode(';', $codes)."m";
  }

  protected function replaceEndStyle($match)
  {
    return "\033[0m";
  }
}
