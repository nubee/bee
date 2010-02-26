<?php

class nbLogger
{
  static $instance = null;
  private $output = null;

  const ERROR = 0;
  const INFO = 1;
  const COMMENT = 2;
  const QUESTION = 3;

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

  public function log($text)
  {
    $this->output->write($text);
  }

  public static function formatLevel($level)
  {
    switch($level) {
      case nbLogger::ERROR : return 'ERROR';
      case nbLogger::INFO : return 'INFO';
      case nbLogger::COMMENT : return 'COMMENT';
      case nbLogger::QUESTION : return 'QUESTION';
    }

    throw new RangeException("[nbLogger::formatLevel] Undefined level: " . $level);
  }
}