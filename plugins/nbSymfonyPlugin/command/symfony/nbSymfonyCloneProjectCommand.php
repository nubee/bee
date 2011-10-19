<?php

class nbSymfonyCloneProjectCommand extends nbCommand {

  protected function configure() {
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

  protected function execute(array $arguments = array(), array $options = array()) {
    $destination = $arguments['to'] . '/' . $arguments['project-name'] . '/';

    $exclude = array(
      'nbproject', '.netbeans', 'cache', 'log'
    );
    $files = nbFileFinder::create('any')
      ->discard($exclude)
      ->prune($exclude)
      ->remove('.')
      ->remove('..')
      ->in($arguments['from']);

    $cloneExists = is_dir($destination);
    if($cloneExists) {
      $finder = nbFileFinder::create('file');

      $files = array_diff($files, $finder->add('*.yml')->in($arguments['from'].'/config'));
      $files = array_diff($files, $finder->add('*')->in($arguments['from'].'/web/images'));
      $files = array_diff($files, $finder->add('*.css')->in($arguments['from'].'/web/css'));
      $files = array_diff($files, $finder->add('*.js')->in($arguments['from'].'/web/js'));
      $files = array_diff($files, $finder->add('*.yml')->in($arguments['from'].'/apps/frontend/config'));
      $files = array_diff($files, $finder->add('*.yml')->in($arguments['from'].'/apps/admin/config'));
    }    
    $sourceProjectName = basename($arguments['from']);
    
    $verbose = isset($options['verbose']) && $options['verbose'];
    $count = 0;
    foreach ($files as $file) {
      $dest = preg_replace('/^.+'.$sourceProjectName.'\//', $destination, $file);
      if (is_dir($file)) {
        if($verbose) 
          $this->log('dir+: ' . $dest, sfLogger::INFO);
        nbFileSystem::mkdir($dest, true);
      }
      else {
        if($verbose) 
          $this->log('file+: ' . $dest, sfLogger::INFO);
        nbFileSystem::copy($file, $dest, true);
      }
      
      if(!$verbose && ($count++ % 100) == 0)
        $this->log('.');
    }
    
    if(!$cloneExists) {
      $files = nbFileFinder::create('file')->remove('.')->remove('..')->in($destination);
      nbFileSystem::replaceTokens($sourceProjectName, $arguments['project-name'], $files);
    }
    
    return true;
  }
}