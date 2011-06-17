<?php

class nbSymfonyCheckSiteCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:check-site')
            ->setBriefDescription('Check a website with a given http code')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('web-site', nbArgument::REQUIRED, 'Web site url'),
                new nbArgument('http-code', nbArgument::REQUIRED, 'Http code expected'),
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Check site '. $arguments['web-site']) ;
    $ch = curl_init($arguments['web-site']);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    # curl_setopt($ch, CURLOPT_HEADER, true);
    # curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_exec($ch);
    if (!curl_errno($ch)) {
      $info = curl_getinfo($ch);
      if ($info['http_code'] == $arguments['http-code']) {
        curl_close($ch);
        $this->logLine('End Check site');
        return true;
      }
    }
    curl_close($ch);
    return false;
  }

}