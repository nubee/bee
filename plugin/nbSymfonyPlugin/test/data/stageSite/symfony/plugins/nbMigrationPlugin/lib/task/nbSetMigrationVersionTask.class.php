<?php

class nbSetMigrationVersionTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
     // add your own arguments here
     $this->addArguments(array(
       new sfCommandArgument('migration_version', sfCommandArgument::REQUIRED, 'Migration version to set'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'set-migration-version';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [bee:set-migration-version|INFO] task does things.
Call it with:

  [php symfony doctrine:set-migration-version|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    if(preg_match('/^\d+$/', $arguments['migration_version']) == 0)
      throw new Exception('Wrong parameter: migration version must be an int');
    $migration = new Doctrine_Migration();
    $migration->setCurrentVersion($arguments['migration_version']);
  }
}
