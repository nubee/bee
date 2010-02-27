<?php

class nbFormatter
{
  public function format($message)
  {
    $message = preg_replace_callback('#<([a-z][a-z0-9\-_]+)>#i', array($this, 'replaceStartStyle'), $message);

    return preg_replace_callback('#</([a-z][a-z0-9\-_]+)>#i', array($this, 'replaceEndStyle'), $message);
  }

  public function formatLine($text) {
    return $this->format($text) + "\n\r";
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
