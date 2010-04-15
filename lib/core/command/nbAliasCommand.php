<?php

/**
 * Defines an alias for another command
 *
 * @package    bee
 * @subpackage command
 */
class nbAliasCommand extends nbApplicationCommand
{
  private $alias;
  private $command;

  public function  __construct($alias, nbCommand $command)
  {
    $this->alias = $alias;
    $this->command = $command;
    parent::__construct();
  }

  protected function configure()
  {
    $this->setName($this->alias)
      ->setBriefDescription($this->command->getBriefDescription())
      ->setDescription($this->command->getDescription());

    $this->setArguments($this->command->getArguments());
    $this->setOptions($this->command->getOptions());
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $r = new ReflectionClass($this->command);
    if($r->isSubclassOf('nbApplicationCommand'))
      $this->command->setApplication($this->getApplication());

    return $this->command->execute($arguments, $options);
  }
}
