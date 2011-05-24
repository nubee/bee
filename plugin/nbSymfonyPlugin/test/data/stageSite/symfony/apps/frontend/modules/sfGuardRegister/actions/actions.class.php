<?php

require_once (sfConfig::get('sf_plugins_dir') . '/sfDoctrineGuardPlugin/modules/sfGuardRegister/lib/BasesfGuardRegisterActions.class.php');

/**
 * sfGuardRegister actions.
 *
 * @package    guard
 * @subpackage sfGuardRegister
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z jwage $
 */
class sfGuardRegisterActions extends BasesfGuardRegisterActions
{
  public function executeNew(sfWebRequest $request)
  {
    if($this->getUser()->isAuthenticated()) {
      $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
      $this->redirect('@homepage');
    }

    $this->form = new sfGuardRegisterFormFrontend();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new sfGuardRegisterFormFrontend();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeActivation(sfWebRequest $request)
  {
    $activationToken = $request->getParameter('token');
    $this->forward404Unless($userProfile = Doctrine_Core::getTable('sfGuardUserProfile')->findOneByValidate($activationToken));
    $user = $userProfile->getUser();
    $user->unlink('Groups');
    $user->addGroupByName('Customers');
    $user->save();
    $this->getUser()->signIn($user);
    $this->redirect('@homepage');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));

    if($form->isValid()) {
      $user = $this->form->save();
      $activationLink = sfConfig::get('app_activation_url') . $user->getProfile()->getValidate();
      $message = $this->getMailer()->compose(
          array('registration@findeal.com' => 'Findeal Registration'),
          $user->getEmailAddress(),
          'Findeal account activation',
          <<<EOF
To fully activate your Findeal account, we need you to verify your email address.
Please click on the link below to confirm your email address and you'll be set.
{$activationLink}

The Findeal Team
EOF
      );

      $this->getMailer()->send($message);
      $this->getUser()->signIn($user);
      $this->redirect('@homepage');
    }
  }

}