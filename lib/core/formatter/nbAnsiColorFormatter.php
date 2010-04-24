<?php

class nbAnsiColorFormatter extends nbFormatter
{
  private $code_stack = array();
  protected
    $styles = array();

  protected static
    $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8),
    $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37),
    $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

  public function  __construct() {
   $this->styles = array(
      'error'    => array('bg' => 'red', 'fg' => 'white', 'bold' => true),
      'info'     => array('fg' => 'green', 'bold' => true),
      'comment'  => array('fg' => 'yellow'),
      'question' => array('bg' => 'cyan', 'fg' => 'black', 'bold' => false),
    );

  }
  public function setStyles($styles)
  {
    $this->styles = $styles;
  }

  protected function replaceStartStyle($match)
  {
    if (!isset($this->styles[strtolower($match)]))
      throw new InvalidArgumentException(sprintf('[nbAnsiColorFormatter::replaceStartStyle] Unknown style "%s".', $match));

    $parameters = $this->styles[strtolower($match)];
    $codes = array();

    if (isset($parameters['fg']))
      $codes[] = self::$foreground[$parameters['fg']];

    if (isset($parameters['bg']))
      $codes[] = self::$background[$parameters['bg']];

    foreach (self::$options as $option => $value) {
      if (isset($parameters[$option]) && $parameters[$option])
        $codes[] = $value;
    }
    $code = "\033[".implode(';', $codes)."m";
    $this->code_stack[] = $code;
    return $code;
  }

  protected function replaceEndStyle($match)
  {
    array_pop($this->code_stack);
    $last = $this->code_stack[count($this->code_stack)-1] ;
    return ($last == null)?"\033[0m":$last;
  }
}
