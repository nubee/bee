<?php

class nbSvnClient
{
  public function getMkdirCmdLine($path, $message, $username = '', $password = '')
  {
    $command = 'svn mkdir ';
    $command .= '"' . $path . '" ';
    $command .= '--message "' . $message . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

  public function getDelCmdLine($path, $message, $username = '', $password = '')
  {
    $command = 'svn del ';
    $command .= '"' . $path . '" ';
    $command .= '--message "' . $message . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

//  function propset($property, $value) {
//    $command .= 'svn propset ';
//    $command .= '"' . $value . '" "' . $path . '" ';
//    if ($this->username != '' && $this->password != '')
//      $command .= '--username ' . $this->username . ' --password ' . $this->password;
//
//    return $this->executor->execute(trim($command));
//  }

  public function getImportCmdLine($path, $repository, $message, $username = '', $password = '')
  {
    $command = 'svn import ';
    $command .= '"' . $path . '" "' . $repository . '" ';
    $command .= '--message "' . $message . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

  public function getCheckoutCmdLine($repository, $path, $force = false, $username = '', $password = '')
  {
    $command = 'svn checkout ';
    $command .= '"' . $repository . '" "' . $path . '" ';
    if ($force === true)
      $command .= '--force ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

  public function getCommitCmdLine($path, $message, $username = '', $password = '')
  {
    $command = 'svn commit ';
    $command .= '"' . $path . '" ';
    $command .= '--message "' . $message . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

  public function getUpdateCmdLine($path, $username = '', $password = '')
  {
    $command = 'svn update ';
    $command .= '"' . $path . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }

  public function getStatusCmdLine($path, $username = '', $password = '')
  {
    $command = 'svn status ';
    $command .= '"' . $path . '" ';
    if (('' != $username) && ('' != $password))
      $command .= '--username ' . $username . ' --password ' . $password;

    return trim($command);
  }
}
