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
      new nbOption('config', 'c', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'Changes the configuration properties'),
      new nbOption('enable-plugin', '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'Enable a plugin'),
      new nbOption('file', '', nbOption::PARAMETER_REQUIRED, 'Read configuration from FILE'),
    ));

  }
}
