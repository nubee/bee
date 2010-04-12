<?php
class nbCommandLoaderWithReset extends nbCommandLoader
{
  public function reset() {
    $this->commands = new nbCommandSet();
  }
  public function addCommands($commands) {
    $this->commands = new nbCommandSet($commands);
  }
}