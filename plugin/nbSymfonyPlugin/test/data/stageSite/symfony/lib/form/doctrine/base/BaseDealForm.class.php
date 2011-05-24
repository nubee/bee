<?php

/**
 * Deal form base class.
 *
 * @method Deal getObject() Returns the current form's model object
 *
 * @package    findeal
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'company_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Company'), 'add_empty' => false)),
      'deal_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DealType'), 'add_empty' => false)),
      'name'         => new sfWidgetFormInputText(),
      'description'  => new sfWidgetFormTextarea(),
      'image'        => new sfWidgetFormInputText(),
      'price'        => new sfWidgetFormInputText(),
      'discount'     => new sfWidgetFormInputText(),
      'published_at' => new sfWidgetFormDateTime(),
      'start_at'     => new sfWidgetFormDateTime(),
      'end_at'       => new sfWidgetFormDateTime(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'company_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Company'))),
      'deal_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DealType'))),
      'name'         => new sfValidatorString(array('max_length' => 255)),
      'description'  => new sfValidatorString(array('required' => false)),
      'image'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price'        => new sfValidatorNumber(),
      'discount'     => new sfValidatorNumber(),
      'published_at' => new sfValidatorDateTime(array('required' => false)),
      'start_at'     => new sfValidatorDateTime(),
      'end_at'       => new sfValidatorDateTime(),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('deal[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Deal';
  }

}
