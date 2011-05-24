<?php

class profileActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->user = $this->getUser();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->form = new sfGuardUserProfileForm($this->getUser()->getProfile());
    $this->setTemplate('edit');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->form = new sfGuardUserProfileForm($this->getUser()->getProfile());
    $this->processForm($request, $this->form);
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
      $form->save();
  }
}
