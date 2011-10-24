<?php

class nbShell
{
  private
    $returnCode,
    $output,
    $error;

  public function __construct()
  {
    $this->returnCode = null;
  }

  public function execute($command, $doit = true)
  {
    if($doit) {
      $this->output = system($command, $this->returnCode);
      return ($this->returnCode === 0);
    }
    else {
      nbLogger::getInstance()->logLine($command);
      return true;
    }
  }

  public function getOutput()
  {
    return $this->output;
  }

  public function getError()
  {
    return $this->error;
  }

  public function getReturnCode()
  {
    return $this->returnCode;
  }
}
