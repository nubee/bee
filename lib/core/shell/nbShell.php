<?php

class nbShell
{
  private
    $captureStdErr,
    $output;

  public function __construct($captureStdErr = false)
  {
    $this->captureStdErr = $captureStdErr;
  }

  public function execute($command, array &$output = null)
  {
    $result = 0;
    if(null !== $this->captureStdErr)
      exec($command . ' 2>&1', $this->output, $result);
    else
      system($command . ' 2>&1', $result);
    
    return ($result == 0) ? true : false;
  }

  public function getOutput()
  {
    return $this->output;
  }
}
