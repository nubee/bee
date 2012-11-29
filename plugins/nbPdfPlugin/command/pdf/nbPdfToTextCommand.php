<?php

class nbPdfToTextCommand extends nbCommand
{

    protected function configure()
    {
        $this->setName('pdf:text')
            ->setBriefDescription('Extract text from a pdf file')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

        $this->setArguments(new nbArgumentSet(array(
            new nbArgument('pdf-file', nbArgument::REQUIRED, 'The pdf file'),
        )));

        $this->setOptions(new nbOptionSet(array(
            new nbOption('unicode', 'u', nbOption::PARAMETER_NONE, 'Use unicode encoding'),
        )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        $file = $arguments['pdf-file'];

        $this->logLine(sprintf('Extracting text from pdf file: %s', $file), nbLogger::COMMENT);

        $pdf2text = new PDF2Text();
        $pdf2text->setFilename($file);
        $pdf2text->setUnicode(isset($options['unicode']));
        $pdf2text->decodePDF();
        $output = $pdf2text->output();

        file_put_contents(sprintf('%s.txt', $file), $output);

        $this->logLine('Text extracted!', nbLogger::COMMENT);
    }

}