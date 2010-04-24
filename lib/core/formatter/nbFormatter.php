<?php

class nbFormatter
{
  public function format($message)
  {
    return preg_replace_callback('#<(/?)([a-z][a-z0-9\-_]+)>#i', array($this, 'replaceStyle'), $message);
//    $message = preg_replace_callback('#<([a-z][a-z0-9\-_]+)>#i', array($this, 'replaceStartStyle'), $message);
//    return preg_replace_callback('#</([a-z][a-z0-9\-_]+)>#i', array($this, 'replaceEndStyle'), $message);
  }

  public function formatLine($text) {
    return $this->format($text) + "\n\r";
  }

  protected function replaceStyle($matches)
  {
    if($matches[1] == '/')
      return $this->replaceEndStyle($matches[1]);
    return $this->replaceStartStyle($matches[2]);
  }

  protected function replaceStartStyle($match)
  {
    return '';
  }

  protected function replaceEndStyle($match)
  {
    return '';
  }
}
