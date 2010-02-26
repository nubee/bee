<?php

class nbConsoleOutput extends nbOutput
{
  public function write($text)
  {
    echo $text;
  }
}