<?php

class nbShell
{
  private
    $redirectOutput,
    $output,
    $error;

  public function __construct($redirectOutput = false)
  {
    $this->redirectOutput = $redirectOutput;
  }

  public function execute($command, array &$output = null)
  {
    $return = 0;
/*    if(null !== $this->redirectOutput)
      exec($command . ' 2>&1', $this->output, $return);
    else
      system($command . ' 2>&1', $return);
*/

    $descriptors = array(
      0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
      1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
      2 => array("pipe", "w")   // stderr is a pipe that the child will write to
//      2 => STDERR   // stderr is a pipe that the child will write to
    );

    $process = proc_open($command, $descriptors, $pipes);
    if(!is_resource($process))
      throw new LogicException('Process cannot be spawned');

    ob_start();
    $this->output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $this->error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $return = proc_close($process);
//    print_r($pipes[1])
    
    return ($return == 0) ? true : false;
  }

  public function getOutput()
  {
    return $this->output;
  }

  public function getError()
  {
    return $this->error;
  }
}
