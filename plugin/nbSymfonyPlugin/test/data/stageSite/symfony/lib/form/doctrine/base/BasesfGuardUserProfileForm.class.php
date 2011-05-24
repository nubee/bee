<?php

/**
 * sfGuardUserProfile form base class.
 *
 * @method sfGuardUserProfile getObject() Returns the current form's model object
 *
 * @package    findeal
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasesfGuardUserProfileForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'birthday'     => new sfWidgetFormDate(),
      'address'      => new sfWidgetFormInputText(),
      'zip_code'     => new sfWidgetFormInputText(),
      'country'      => new sfWidgetFormInputText(),
      'nationality'  => new sfWidgetFormInputText(),
      'phone_number' => new sfWidgetFormInputText(),
      'fax_number'   => new sfWidgetFormInputText(),
      'website'      => new sfWidgetFormInputText(),
      'validate'     => new sfWidgetFormInputText(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'birthday'     => new sfValidatorDate(array('required' => false)),
      'address'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'zip_code'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'country'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nationality'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phone_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'fax_number'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'website'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'validate'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'sfGuardUserProfile', 'column' => array('user_id')))
    );

    $this->widgetSchema->setNameFormat('sf_guard_user_profile[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfGuardUserProfile';
  }

}
