<?php

class nbProcShell
{
  private
    $redirectOutput,
    $returnCode,
    $output,
    $error;

  public function __construct($redirectOutput = false)
  {
    $this->redirectOutput = $redirectOutput;
    $this->returnCode = null;
  }

  public function execute($command)
  {
    $descriptors = array(
      0 => array("pipe", "r"),
      1 => array("pipe", "w"),
      2 => array("pipe", "w")
    );
//    $command = 'start ' . $command;
    $process = proc_open($command, $descriptors, $pipes);

    if(!is_resource($process))
      throw new LogicException('Process cannot be spawned');

    $stdoutDone = null;
    $stderrDone = null;
    while (true) {
      $rx = array(); // The program's stdout/stderr

      if (!$stdoutDone) $rx[] = $pipes[1];
      if (!$stderrDone) $rx[] = $pipes[2];

//      echo "stream_select: " . stream_select($rx, $tx = array(), $ex = array($rx[0]), 0) . "\n";
      stream_select($rx, $tx = array(), $ex = array(), 10);
      
      foreach ($rx as $r) {
        if ($r == $pipes[1]) {
          $res = fgets($pipes[1]);
          $this->output .= $res;
          if(!$this->redirectOutput)
            echo $res;
          if (!$stdoutDone && feof($pipes[1])) {
            fclose($pipes[1]); $stdoutDone = true;
          }
        }
        if ($r == $pipes[2]) {
          $res = fgets($pipes[2]);
          $this->error .= $res;
          if(!$this->redirectOutput)
            echo $res;
          if (!$stderrDone && feof($pipes[2])) {
            fclose($pipes[2]); $stderrDone = true;
          }
        }
      }

      if($stdoutDone && $stderrDone)
        break;
    }

    $this->returnCode = proc_close($process);
    
//    if(0 !== $this->returnCode) {
//      throw new LogicException(sprintf(
//        '[nbShell::execute] Command "%s" exited with error code %s',
//        $command, $this->returnCode
//      ));
//    }

    return ($this->returnCode === 0);
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
