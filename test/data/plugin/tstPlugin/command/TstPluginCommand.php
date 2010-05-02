<?php

class TstPluginCommand extends nbCommand
{
  public function configure(){
        $this->setName('TstPluginCommand');
  }
  public function execute(array $arguments = array(), array $options = array()){return 0;}
}
