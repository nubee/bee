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

    $this->form = new sfGuardRegisterFormAdmin();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $params  = $request->getParameter('sf_guard_user');
    $email = $params['email_address'];
    
    if( $user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress($email)){
      $this->processUserRequest($request, $user,$params['password']);
    }
    else {
      $this->form = new sfGuardRegisterFormAdmin();

      $this->processForm($request, $this->form);

    }
    $this->setTemplate('new');
  }

  public function executeThankYou(sfWebRequest $request)
  {

  }

  public function executeActivation(sfWebRequest $request)
  {
    $activationToken = $request->getParameter('token');
    $this->forward404Unless($userProfile = Doctrine_Core::getTable('sfGuardUserProfile')->findOneByValidate($activationToken));
    $user = $userProfile->getUser();
    $user->setIsActive(1);
    $user->unlink('Groups');
    $user->addGroupByName('Managers');
    $user->save();
    $this->getUser()->signIn($user);
    $this->redirect('@homepage');
  }
  
  protected function sendActivationMail(sfWebRequest $request, $user)
  {
      $activationLink = sfConfig::get('app_activation_url') . $user->getProfile()->getValidate();
      $message = $this->getMailer()->compose(
          array('registration@findeal.com' => 'Findeal Registration'),
          $user->getEmailAddress(),
          'Findeal manager account activation',
          <<<EOF
To fully activate your Findeal account, we need you to verify your email address.
Please click on the link below to confirm your email address and you'll be set.
{$activationLink}

The Findeal Team
EOF
      );

      $this->getMailer()->send($message);

  }

  protected function sendWelcomeMail($request, $user)
  {
            $findealAdminLink = sfConfig::get('app_admin_url');
            $message = $this->getMailer()->compose(
                array('registration@findeal.com' => 'Findeal Registration'),
                $user->getEmailAddress(),
                'Findeal manager account activated',
              <<<EOF
Your Findeal manager account is active, you can admnister your Findeal Store at
{$findealAdminLink}

The Findeal Team
EOF

            );
            $this->getMailer()->send($message);

  }

  protected function processUserRequest(sfWebRequest $request, $user, $password)
  {
        if($user->checkPassword($password) and $user->getIsActive() ){
          if($user->hasGroup('Customers') and !$user->hasGroup('Managers')){
            $user->unlink('Groups');
            $user->addGroupByName('Managers');
            $user->save();
            $user->refresh(true);
            $this->sendWelcomeMail($request, $user);
          }
          else if($user->hasGroup('LimitedCustomers') and !$user->hasGroup('Managers')){
            $this->sendActivationMail($request, $user);
            $this->redirect('sf_guard_register_thank_you', $user);
          }

          $this->getUser()->signIn($user);
          
        }
        $this->redirect('@homepage');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {

    $form->bind($request->getParameter($form->getName()));
    if($form->isValid()) {
      $user = $this->form->save();
      $this->sendActivationMail($request, $user);
      $this->redirect('sf_guard_register_thank_you', $user);
    }
  }
}
