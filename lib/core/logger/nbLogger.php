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

  protected function __construct()
  {
    $this->output = new nbConsoleOutput();
  }
  
  public function getInstance()
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
    if($level)
      $text = $this->format($text, $level);
    $this->output->write($text);
  }

  public function format($text, $level)
  {
    $level = self::formatLevel($level);
    return sprintf('<%s>%s</%s>', $level, $text, $level);
  }

  public function formatLine($text, $level)
  {
    return $this->format($text, $level) . "\n";
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
}