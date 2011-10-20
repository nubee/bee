<?php

class nbSymfonyCheckSiteCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:check-site')
      ->setBriefDescription('Checks a website with a given http code')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('web-site', nbArgument::REQUIRED, 'Web site url'),
        new nbArgument('http-status-code', nbArgument::REQUIRED, 'Expected HTTP status code'),
      )));

    $this->setOptions(new nbOptionSet(array(
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $website = $arguments['web-site'];
    $statusCode = $arguments['http-status-code'];
    
    $this->logLine(sprintf('Checking %s with code %s', $website, $statusCode));
    
    $ch = curl_init($website);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    # curl_setopt($ch, CURLOPT_HEADER, true);
    # curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_exec($ch);
    
    if(!curl_errno($ch)) {
      $info = curl_getinfo($ch);
      if($info['http_code'] == $statusCode) {
        curl_close($ch);
        $this->logLine('Check site successfull!');
        
        return true;
      }
    }
    
    curl_close($ch);
    
    $this->logLine('Check site not successfull!', nbLogger::INFO);
    return false;
  }

}