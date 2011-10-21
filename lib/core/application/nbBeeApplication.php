<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbBeeApplication extends nbApplication
{
  protected function configure()
  {
    $this->name = 'bee';
    $this->version = '0.1.0';

    $this->options->addOptions(array(
      new nbOption('config',             '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'Changes the configuration properties'),
      new nbOption('enable-plugin',      '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'Enables a plugin'),
      new nbOption('enable-all-plugins', '', nbOption::PARAMETER_NONE,                          'Enables all plugins'),
    ));

  }
}
