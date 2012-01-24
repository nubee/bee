<?php

class nbRepositoryCommitCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('repository:commit')
      ->setBriefDescription('Commit work into the remote repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'Commit message'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('interactive', 'i', nbOption::PARAMETER_NONE, 'Interactive mode'),
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Force push'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $interactiveMode = isset($options['interactive']) ? true : false;
    $forceMode = isset($options['force']) ? true : false;
    $message = $arguments["message"];
    $buildMessage = "Update build version";
    $repositoryType = nbConfig::get('project_repository_type', 'git');
    
    if(nbConfig::has('project_repository_root-directory')) {
      $projectRootDir = nbConfig::get('project_repository_root-directory');
    }
    else {
      throw new Exception('You must configure your project root directory');
    }
    $buildVersionFile = $projectRootDir . "/version.yml";
    
    if(nbConfig::has('project_repository') and $repositoryType == 'git') {

      $shell = new nbShell();
      $this->logLine(sprintf('Starting repository commit for project %', nbConfig::get('project_name')));
      // git add <project root dir>
      $this->logLine(sprintf('adding working dir %s to stage', $projectRootDir));
      $cmd = sprintf('git add %s', $projectRootDir);
      $code = $shell->execute($cmd);
      if($code == 0 and !$this->askConfirmation(sprintf('Error in "%s", do you want to continue anyway?', $cmd))) {
        $this->logLine('Bye');
        die;
      }
      
      // git status
      $this->logLine('git status');
      $cmd = sprintf('git status', $projectRootDir);
      $code = $shell->execute($cmd);
      
      // git commit -m "<commit message>"
      if($interactiveMode and !$this->askConfirmation("Do you want to commit your changes to your local repository?")) {
        $this->logLine('Bye');
        die;
      }
      $this->logLine(sprintf('commiting working dir %s to local repository', $projectRootDir));
      $cmd = sprintf('git commit -m "%s"', $message);
      $code = $shell->execute($cmd);

      // git pull
      if($interactiveMode and !$this->askConfirmation("Do you want to update from your remote repository?")) {
        $this->logLine('Bye');
        die;
      }
      $this->logLine('updating local repository with remote changes');
      $cmd = 'git pull';
      $code = $shell->execute($cmd);
      if($code == 0) {
        $this->logLine('git pull failed');
        die;
      }

      // bee test:all
      if(!$interactiveMode or ($interactiveMode and $this->askConfirmation("Do you want to test your project?"))) {
        $this->logLine('executing nbTestAllCommand');
        $cmd = new nbTestAllCommand();
        $cmd->setApplication($this->getApplication());
        $code = $cmd->run(new nbCommandLineParser(), '');
        if(($code == 0 and !$interactiveMode and !$forceMode) or ($code == 0 and $interactiveMode and !$this->askConfirmation('There are some errors in your tests, do you want to continue anyway?'))) {
          $this->logLine('Bye');
          die;
        }
      }

      // commit repository updating build file
      if($interactiveMode and !$this->askConfirmation("Do you want to commit your changes in the remote repository?")) {
        $this->logLine('Bye');
        die;
      }

      // bee version:update-build <build version file>
      $cmd = new nbUpdateBuildVersionCommand();
      $cmdLine = sprintf('%s', $buildVersionFile);
      $parser = new nbCommandLineParser();
      $cmd->run($parser, $cmdLine);

      // git add <build version file>
      $this->logLine('adding build version file');
      $cmd = sprintf('git add %s', $buildVersionFile);
      $code = $shell->execute($cmd);
      if($code == 0 and !$this->askConfirmation(sprintf('Error in "%s", do you want to continue anyway?', $cmd))) {
        $this->logLine('Bye');
        die;
      }

      // git commit -m "<commit build message>"
      $this->logLine('updating local repository with remote changes');
      $cmd = sprintf('git commit -m "%s"', $buildMessage);
      $code = $shell->execute($cmd);
      if($code == 0 and !$this->askConfirmation(sprintf('Error in "%s", do you want to continue anyway?', $cmd))) {
        $this->logLine('Bye');
        die;
      }

      // git push
      $this->logLine('merging remote repository with local repository');
      $cmd = 'git push';
      $code = $shell->execute($cmd);
      if($code == 0) {
        $this->logLine('Push error');
        die;
      }
      $this->logLine('Repository commit executed succesfully');
    }
  }

  private function askConfirmation($message)
  {
    fwrite(STDOUT, $message . "\n");
    $response = fgets(STDIN);
    if(strtolower(trim($response)) != 'y') {
      return false;
    }
    return true;
  }

}