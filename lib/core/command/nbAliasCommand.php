<?php

/**
 * Defines an alias for another command
 *
 * @package    bee
 * @subpackage command
 */
class nbAliasCommand extends nbCommand
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
    return $this->command->execute($arguments, $options);
  }
}
