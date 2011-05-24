<?php

class mainActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executePrivate(sfWebRequest $request)
  {
    $this->username = $this->getUser()->getGuardUser()->getUsername();
  }

}
