<?php

class nbGetLatestMigrationVersionTask extends sfDoctrineBaseTask
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
    $this->name             = 'get-latest-migration-version';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [doctrine:get-latest-migration-version|INFO] task does things.
Call it with:

  [php symfony bee:get-migration-latest-version|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
   $databaseManager = new sfDatabaseManager($this->configuration);

    $config = $this->getCliConfig();
    $migration = new Doctrine_Migration($config['migrations_path']);
    $version = $migration->getLatestVersion();
    echo $version;
  }
}
