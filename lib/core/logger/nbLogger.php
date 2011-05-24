<?php

class nbLogger
{
  static $instance = null;
  private $output = null;

  const NONE = 0;
  const ERROR = 1;
  const INFO = 2;
  const COMMENT = 3;
  const QUESTION = 4;

  const endl = "\n";

  protected function __construct()
  {
    $this->output = new nbConsoleOutput();
  }
  
  public static function getInstance()
  {
    if(self::$instance == null)
      self::$instance = new self();

    return self::$instance;
  }

  public function setOutput(nbOutput $output)
  {
    $this->output = $output;
  }

  public function log($text, $level = null)
  {
    if(is_array($text))
      $text = $this->formatArray($text, $level);
    elseif($level)
      $text = $this->format($text, $level);
    $this->output->write($text);
  }

  public function logLine($text, $level = null)
  {
    if($level)
      $text = $this->format($text, $level);
    $this->output->write($text . self::endl);
  }

  public function format($text, $level)
  {
    $level = self::formatLevel($level);
    return sprintf('<%s>%s</%s>', $level, $text, $level);
  }

  public function formatLine($text, $level)
  {
    return $this->format($text, $level) . self::endl;
  }

  public static function formatLevel($level)
  {
    switch($level) {
      case nbLogger::ERROR : return 'error';
      case nbLogger::INFO : return 'info';
      case nbLogger::COMMENT : return 'comment';
      case nbLogger::QUESTION : return 'question';
    }

    throw new RangeException("[nbLogger::formatLevel] Undefined level: " . $level);
  }

  private function formatArray(array $array, $level = null)
  {
    $text = '';
    foreach ($array as $key => $value)
    {
      $text .= ($level)
        ? $this->format($key, $level) . ' => ' . $this->format($value, $level) . self::endl
        : $key . ' => ' . $value . self::endl;
    }
    return $text;
  }
}