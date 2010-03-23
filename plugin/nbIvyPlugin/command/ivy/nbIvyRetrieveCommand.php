<?php

class nbIvyRetrieveCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('ivy:retrieve')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('ivyfile', nbArgument::OPTIONAL, 'Ivy file', 'ivy.xml')
      )))
      ->setBriefDescription('Retrieves dependencies from the given ivy file')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> retrieves dependencies:

    <info>./bee {$this->getFullName()} ivyfile</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $client = new nbIvyClient();

    $this->log('Retrieving dependencies...', nbLogger::COMMENT);
    $this->log("\n");
    $command = $client->getRetrieveCmdLine();

//    echo $command . "\n"; die;

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbIvyRetrieveCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
//    $zip = new ZipArchive();
//    foreach ($files as $value) {
//      if ($zip->open($value, ZIPARCHIVE::CHECKCONS) !== true) {
//        echo '[RetrieveTask::execute] : Error opening file ' . $value;
//        return false;
//      }
//
//      $zip->extractTo(dirname($value));
//      $zip->close();
//      echo $value . "\n";
//    }
  }
}
