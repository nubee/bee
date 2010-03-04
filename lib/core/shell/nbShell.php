<?php

class nbShell
{
  function execute($command, array &$output = null)
  {
    if(null !== $output)
      exec($command . ' 2>&1', $output, $return_var);
    else
      system($command . ' 2>&1', $return_var);
    
    return $return_var == 0 ? true : false;
  }
}
