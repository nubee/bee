<?php

class nbGenerateVirtualHostCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('apache:generate-vhost')
      ->setBriefDescription('Creates a virtualhost file')
      ->setDescription(<<<TXT
 Generate a Virtual host file.
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('name', nbArgument::REQUIRED, 'The server name (without TLD)'),
      new nbArgument('path', nbArgument::REQUIRED, 'The project path'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('tld', '', nbOption::PARAMETER_OPTIONAL, 'Set the Top Level Domain','localhost'),
      new nbOption('type', '', nbOption::PARAMETER_OPTIONAL, 'Set project type','standard'),
      new nbOption('alias', '', nbOption::PARAMETER_REQUIRED|nbOption::IS_ARRAY, 'Set ServerAlias'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $serverName = $arguments['name'];
    $projectPath = $arguments['path'];

    $tld = isset ($options['tld'])? $options['tld'] : 'localhost' ;
    $alias = $options['alias'];

    $type = isset ($options['type'])? $options['type'] : 'standard' ;

    $index = 'index.php';

    $template = dirname(__FILE__).'/../../data/template/vhost-'.$type.'.tpl';
    if(! file_exists($template))
      throw new InvalidArgumentException('[nbGenerateVirtualHostCommand:execute] template file '.$template.' not found');
    ob_start();
    include($template);
    $out = ob_get_contents();
    ob_end_clean();
    echo $out;
  }

}