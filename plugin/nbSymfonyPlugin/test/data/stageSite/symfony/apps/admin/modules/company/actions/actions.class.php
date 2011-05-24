<?php

/**
 * company actions.
 *
 * @package    findeal
 * @subpackage company
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class companyActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
     $this->companies = $this->getUser()->getCompanies();
  }

  public function executeShow(sfWebRequest $request)
  {
     $this->forward404Unless(
         $this->company = Doctrine_Core::getTable('Company')->find(array($request->getParameter('id'))), sprintf('Object company does not exist (%s).', $request->getParameter('id')));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new CompanyForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->form = new CompanyForm();
    $this->form->setDefault('user_id',$this->getUser()->getGuardUser()->getId());
    $this->processForm($request, $this->form);
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
          $company = Doctrine_Core::getTable('Company')->find(array($request->getParameter('id'))), sprintf('Object Company does not exist (%s).', $request->getParameter('id')));
    $this->form = new CompanyForm($company);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($company = Doctrine_Core::getTable('Company')->find(array($request->getParameter('id'))), sprintf('Object company does not exist (%s).', $request->getParameter('id')));
    $this->form = new CompanyForm($company);
    $this->processForm($request, $this->form);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $this->forward404Unless($company = Doctrine_Core::getTable('Company')->find(array($request->getParameter('id'))), sprintf('Object company does not exist (%s).', $request->getParameter('id')));
    $company->delete();
    $this->redirect('company/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $array_form = $request->getParameter($form->getName());
    $array_form['user_id'] = $this->getUser()->getGuardUser()->getId();
    $form->bind($array_form, $request->getFiles($form->getName()));
    if ($form->isValid())
    {
       $form->save();
       $this->setTemplate('edit');
    }
    else
       $this->setTemplate('new');
  }
}
