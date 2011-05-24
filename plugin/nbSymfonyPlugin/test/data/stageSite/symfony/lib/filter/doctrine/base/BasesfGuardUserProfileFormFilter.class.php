<?php

/**
 * sfGuardUserProfile filter form base class.
 *
 * @package    findeal
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasesfGuardUserProfileFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'birthday'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'address'      => new sfWidgetFormFilterInput(),
      'zip_code'     => new sfWidgetFormFilterInput(),
      'country'      => new sfWidgetFormFilterInput(),
      'nationality'  => new sfWidgetFormFilterInput(),
      'phone_number' => new sfWidgetFormFilterInput(),
      'fax_number'   => new sfWidgetFormFilterInput(),
      'website'      => new sfWidgetFormFilterInput(),
      'validate'     => new sfWidgetFormFilterInput(),
      'created_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'user_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'birthday'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'address'      => new sfValidatorPass(array('required' => false)),
      'zip_code'     => new sfValidatorPass(array('required' => false)),
      'country'      => new sfValidatorPass(array('required' => false)),
      'nationality'  => new sfValidatorPass(array('required' => false)),
      'phone_number' => new sfValidatorPass(array('required' => false)),
      'fax_number'   => new sfValidatorPass(array('required' => false)),
      'website'      => new sfValidatorPass(array('required' => false)),
      'validate'     => new sfValidatorPass(array('required' => false)),
      'created_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('sf_guard_user_profile_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfGuardUserProfile';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'user_id'      => 'ForeignKey',
      'birthday'     => 'Date',
      'address'      => 'Text',
      'zip_code'     => 'Text',
      'country'      => 'Text',
      'nationality'  => 'Text',
      'phone_number' => 'Text',
      'fax_number'   => 'Text',
      'website'      => 'Text',
      'validate'     => 'Text',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
    );
  }
}
