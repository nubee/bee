<?php

class AnsiColorFormatter extends Formatter
{
  protected
    static $styles = array(
      'ERROR'    => array('bg' => 'red', 'fg' => 'white', 'bold' => true),
      'INFO'     => array('fg' => 'green', 'bold' => true),
      'COMMENT'  => array('fg' => 'yellow'),
      'QUESTION' => array('bg' => 'cyan', 'fg' => 'black', 'bold' => false),
    ),
    $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8),
    $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37),
    $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

  public function format($text, $style = 'INFO', $options = array()) {
    $codes = array();
    if(isset(self::$styles[$style]['fg']))
      $codes[] = self::$foreground[self::$styles[$style]['fg']];
    
    if(isset(self::$styles[$style]['bg']))
      $codes[] =  self::$background[self::$styles[$style]['bg']];

    foreach(self::$options as $name => $value)
      if(isset(self::$styles[$style][$name]))
        $codes[] = self::$options[$name];

    return "\033[" . implode($codes, ';') . "m" . $text . "\033[0m";
  }
}
