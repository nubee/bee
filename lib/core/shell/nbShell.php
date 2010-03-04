<?php

class nbShell
{
  function execute($command, array &$output = null)
  {
    $result = 0;
    if(null !== $output)
      exec($command . ' 2>&1', $output, $result);
    else
      system($command . ' 2>&1', $result);
    
    return ($result == 0) ? true : false;
  }
}
