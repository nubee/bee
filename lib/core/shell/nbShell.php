<?php

class nbShell
{
  private
    $redirectOutput,
    $output,
    $error;

  public function __construct($redirectOutput = false)
  {
    //echo sprintf("output redirected: %s\n", ($redirectOutput ? 'true' : 'false'));
    $this->redirectOutput = $redirectOutput;
  }

  public function execute($command, array &$output = null)
  {
//    stream_filter_prepend(STDOUT, "string.rot13");
//    stream_filter_prepend(STDERR, "string.rot13");

//    stream_filter_append(STDOUT, "string.toupper");

//    echo "test";
    $descriptors = array();
//    if($this->redirectOutput) {
      $descriptors[] = array("pipe", "r");  // stdin is a pipe that the child will read from
      $descriptors[] = array("pipe", "w");  // stdout is a pipe that the child will write to
      $descriptors[] = array("pipe", "w");   // stderr is a pipe that the child will write to
/*    }
    else {
      $descriptors[] = STDIN;
      $descriptors[] = STDOUT;
      $descriptors[] = STDERR;
    }
*/
    $process = proc_open($command, $descriptors, $pipes);
    if(!is_resource($process))
      throw new LogicException('Process cannot be spawned');

    while(($stdout = !feof($pipes[1])) && ($stderr = !feof($pipes[2]))) {
      if(true === $this->redirectOutput) {
        if($stdout)
          $this->output .= fgets($pipes[1]);
        if($stderr)
          $this->error .= fgets($pipes[2]);
      } else {
        if($stdout) {
          $outputLine = fgets($pipes[1]);
          $this->output .= $outputLine;
          echo $outputLine;
        }
        if($stderr) {
          $errorLine = fgets($pipes[2]);
          $this->error .= $errorLine;
          echo $errorLine;
        }
      }
    }

/*    if($this->redirectOutput) {
      fclose($pipes[0]);
      
      $this->output = stream_get_contents($pipes[1]);
      fclose($pipes[1]);

      $this->error = stream_get_contents($pipes[2]);
      fclose($pipes[2]);
    }
*/
    $this->returnCode = proc_close($process);
    if(0 !== $this->returnCode) {
      throw new LogicException(sprintf(
        '[nbShell::execute] Command "%s" exited with error code %s',
        $command, $this->returnCode
      ));
    }

    return ($this->returnCode == 0);
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
