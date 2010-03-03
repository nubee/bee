<?php

class DummyShell
{
  public $returnValue = true;

  function execute($command, array &$output = null)
  {
    return $returnValue;
  }
}
