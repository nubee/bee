<?php
class SecondPluginCommand  extends nbCommand
{
  public function configure(){
        $this->setName('SecondPluginCommand');
  }
  public function execute(array $arguments = array(), array $options = array()){return 0;}
}
