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
        new nbArgument('clone_from', nbArgument::REQUIRED, 'The path of the project to clone.'),
        new nbArgument('cloned_project_name', nbArgument::REQUIRED, 'The name of the cloned project'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('clone-to', 'p', nbOption::PARAMETER_REQUIRED, 'The path where clone project'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $destination = isset($options['clone-to']) ? $options['clone-to'] . '/' : './';
    $destination = $destination . $arguments['cloned_project_name'];

    try {
      $this->recurse_copy($arguments['clone_from'], $destination);
    } catch (Exception $e) {
      $this->log($e->getMessage(), nbLogger::ERROR);
    }
    
    preg_match('/\/([^\/]+)$/', $arguments['clone_from'], $result);
    $projectNameCloneFrom = $result[1];
    
    $finder = nbFileFinder::create('file');

    $properties = $finder->add('properties.ini')->remove('.')->remove('..')->in($destination);
    $databases = $finder->add('databases.yml')->remove('.')->remove('..')->in($destination);
    $files = array_merge($properties, $databases);

    nbFileSystem::replaceTokens($projectNameCloneFrom, $arguments['cloned_project_name'], $files);
    
    return true;
  }

  protected function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
      if (( $file != '.' ) && ( $file != '..' )) {
        if (is_dir($src . '/' . $file)) {
          $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
        } else {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

}