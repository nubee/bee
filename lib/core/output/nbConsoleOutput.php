<?php

class nbConsoleOutput extends nbOutput
{
  public function write($text)
  {
    echo $this->formatter->format($text);
  }
}