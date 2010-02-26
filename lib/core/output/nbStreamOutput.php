<?php

class nbStreamOutput extends nbOutput
{
  private $stream = '';
  
  public function write($text)
  {
    $this->stream .= $text;
  }

  public function getStream()
  {
    return $this->stream;
  }
}