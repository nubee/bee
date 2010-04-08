<?php

/**
 * Manage version build number.
 *
 * @package    VersionPlugin
 * @subpackage command
 */
class nbBuildnumberCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('version:buildnumber')
//      ->setArguments(new nbArgumentSet(array(
//      )))
//      ->setOptions(new nbOptionSet(array(
//        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
//        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
//      )))
      ->setBriefDescription('Manage build number')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command check out a working copy from a repository:

    <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    
  }
}
