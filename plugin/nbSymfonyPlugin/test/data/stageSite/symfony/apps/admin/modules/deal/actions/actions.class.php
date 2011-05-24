<?php

/**
 * deal actions.
 *
 * @package    findeal
 * @subpackage deal
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
     $this->deals = $this->getUser()->getDeals();
  }

  public function executeShow(sfWebRequest $request)
  {
     $this->forward404Unless(
         $this->deal = Doctrine_Core::getTable('Deal')->find(array($request->getParameter('id'))), sprintf('Object deal does not exist (%s).', $request->getParameter('id')));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new DealForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->form = new DealForm();
    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
          $deal = Doctrine_Core::getTable('Deal')->find(array($request->getParameter('id'))), sprintf('Object deal does not exist (%s).', $request->getParameter('id')));
    $this->form = new DealForm($deal);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($deal = Doctrine_Core::getTable('Deal')->find(array($request->getParameter('id'))), sprintf('Object deal does not exist (%s).', $request->getParameter('id')));
    $this->form = new DealForm($deal);
    $this->processForm($request, $this->form);
    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $this->forward404Unless($deal = Doctrine_Core::getTable('Deal')->find(array($request->getParameter('id'))), sprintf('Object deal does not exist (%s).', $request->getParameter('id')));
    $deal->delete();
    $this->redirect('deal/index');
  }

  public function executeCurrent(sfWebRequest $request)
  {
    $this->deals = $this->getUser()->getCurrentDeals();
  }

  public function executeExpiring(sfWebRequest $request)
  {
    $this->deals = $this->getUser()->getExpiringDeals();
  }

  public function executeExpired(sfWebRequest $request)
  {
    $this->deals = $this->getUser()->getExpiredDeals();
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
      $deal = $form->save();
  }
}
