<?php

class nbGetMigrationVersionTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'get-migration-version';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [bee:get-migration-version|INFO] task does things.
Call it with:

  [php symfony doctrine:get-migration-version|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);

    $migration = new Doctrine_Migration();
    echo $migration->getCurrentVersion();
  }
}
