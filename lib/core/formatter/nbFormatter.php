<?php

class nbFormatter
{
  public function format($text, $style = 'INFO') {
    return $text;
  }

  public function formatLine($text, $style = 'INFO') {
    return $this->format($text, $style) + "\n\r";
  }

  public function formatText($text) {
    return preg_replace("/\[(.+?)\|(\w+)\]/se", '$this->format("$1", "$2")', $text);
  }
}
