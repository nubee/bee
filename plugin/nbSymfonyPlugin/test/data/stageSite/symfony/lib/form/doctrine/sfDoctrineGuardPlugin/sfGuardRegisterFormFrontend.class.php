<?php

/**
 * sfGuardRegisterForm for registering new users
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: BasesfGuardChangeUserPasswordForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardRegisterFormFrontend extends sfGuardRegisterForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    parent::configure();

    unset(
      $this['first_name'],
      $this['last_name'],
      $this['username'],
      $this['password_again']
    );
    $this->setValidator('email_address', new sfValidatorAnd(array(
        new sfValidatorEmail(array('required' => true, 'trim' => true)),
        new sfValidatorString(array('required' => true, 'max_length' => 80)),
        new sfValidatorDoctrineUnique(array(
          'model' => 'sfGuardUser',
          'column' => 'email_address'
          ), array('invalid' => 'An account with that email address already exists.'))
      )));
   $this->setValidator('password', new sfValidatorString(array(
      'required' => true,
      'trim' => true,
      'min_length' => 4,
      'max_length' => 128
    ), array(
      'min_length' => 'That password is too short. It must contain a minimum of %min_length% characters.')));

   $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('email_address'))),
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username'))),
      ))
    );
  }

  protected function doSave($con = null)
  {
    $user = $this->getObject();

    $user->setUsername($this->getValue('email_address'));
    //$user->setIsActive(0);

    $user->setProfile(new sfGuardUserProfile());
    $activationToken = self::createActivationToken();
    $user->getProfile()->setValidate($activationToken);

    parent::doSave($con);

    $user->addGroupByName('LimitedCustomers');
  }

  static private function createActivationToken()
  {
    $activationToken = "n";
    for($i = 0; ($i < 16); $i++) {
      $activationToken .= sprintf("%02x", mt_rand(0, 255));
    }
    return $activationToken;
  }

}