<?php

class nbSymfonyCloneProjectCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:clone-project')
      ->setBriefDescription('Clones a symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command clones a symfony project.

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('from', nbArgument::REQUIRED, 'The path of the project to clone.'),
        new nbArgument('to', nbArgument::REQUIRED, 'The path where to save the clone'),
        new nbArgument('project-name', nbArgument::REQUIRED, 'The name of the cloned project'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $fs = $this->getFileSystem();
    
    $from = $arguments['from'];
    $to = $arguments['to'];
    $projectName = $arguments['project-name'];
    
    $destination = sprintf('%s/%s/', $to, $projectName);

    $exclude = array(
      'nbproject', '.netbeans', 'cache', 'log'
    );
    
    $files = nbFileFinder::create('any')
      ->discard($exclude)
      ->prune($exclude)
      ->remove('.')
      ->remove('..')
      ->in($from);

    $cloneExists = is_dir($destination);
    if($cloneExists) {
      $finder = nbFileFinder::create('file');

      $files = array_diff($files, $finder->add('*.yml')->in($from . '/config'));
      $files = array_diff($files, $finder->add('*')->in($from . '/web/images'));
      $files = array_diff($files, $finder->add('*.css')->in($from . '/web/css'));
      $files = array_diff($files, $finder->add('*.js')->in($from . '/web/js'));
      $files = array_diff($files, $finder->add('*.yml')->in($from . '/apps/frontend/config'));
      $files = array_diff($files, $finder->add('*.yml')->in($from . '/apps/admin/config'));
    }
    
    $sourceProjectName = basename($from);

    $verbose = isset($options['verbose']) && $options['verbose'];
    
    $count = 0;
    foreach($files as $file) {
      $dest = preg_replace('/^.+' . $sourceProjectName . '\//', $destination, $file);
      if(is_dir($file)) {
        if($verbose)
          $this->logLine('dir+: ' . $dest, sfLogger::INFO);
        $fs->mkdir($dest, true);
      }
      else {
        if($verbose)
          $this->logLine('file+: ' . $dest, sfLogger::INFO);
        $fs->copy($file, $dest, true);
      }

      if(!$verbose && ($count++ % 100) == 0)
        $this->log('.');
    }

    if(!$cloneExists) {
      $files = nbFileFinder::create('file')->remove('.')->remove('..')->in($destination);
      $fs->replaceTokens($sourceProjectName, $projectName, $files);
    }

    return true;
  }

}