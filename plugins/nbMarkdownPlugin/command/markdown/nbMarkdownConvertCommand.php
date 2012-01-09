<?php

include_once dirname(__FILE__) . '/../../lib/vendor/markdown.php'; 

class nbMarkdownConvertCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('markdown:convert')
      ->setBriefDescription('Converts a Markdown document to HTML')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source', nbArgument::REQUIRED, 'Source filename'),
        new nbArgument('target', nbArgument::REQUIRED, 'Target filename')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Converting markdown file', nbLogger::COMMENT);
    
    $markdown = file_get_contents($arguments['source']);
    
    $output = Markdown($markdown);
    
    file_put_contents($arguments['target'], $output);
    
    $this->logLine('File converted successfully');

    return true;
  }

}