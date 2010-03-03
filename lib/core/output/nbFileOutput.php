<?php

class nbFileOutput extends nbOutput
{
  private $filename;

  public function __construct($filename)
  {
    parent::__construct();
    $this->filename = $filename;
  }
  
  public function write($text)
  {
    fwrite($this->filename, $this->formatter->format($text));
  }
}