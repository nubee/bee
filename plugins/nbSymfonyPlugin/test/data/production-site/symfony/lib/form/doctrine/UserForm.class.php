<?php

/**
 * User form.
 *
 * @package    testapp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserForm extends BaseUserForm
{
  public function configure()
  {
    unset(
      $this['created_at'], $this['updated_at']
    );
    
    $this->setValidator('email', new sfValidatorEmail(
        array('trim' => true), 
        array('invalid' => 'Please enter a valid e-mail address (email@example.com)')));
  }
}
