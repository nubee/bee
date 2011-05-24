<?php

/**
 * Company form base class.
 *
 * @method Company getObject() Returns the current form's model object
 *
 * @package    findeal
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCompanyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'name'            => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormTextarea(),
      'partita_iva'     => new sfWidgetFormInputText(),
      'city'            => new sfWidgetFormInputText(),
      'address'         => new sfWidgetFormInputText(),
      'state'           => new sfWidgetFormInputText(),
      'zipcode'         => new sfWidgetFormInputText(),
      'phone_number'    => new sfWidgetFormInputText(),
      'mobile_number'   => new sfWidgetFormInputText(),
      'fax_number'      => new sfWidgetFormInputText(),
      'email'           => new sfWidgetFormInputText(),
      'logo'            => new sfWidgetFormInputText(),
      'website'         => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'slug'            => new sfWidgetFormInputText(),
      'latitude'        => new sfWidgetFormInputText(),
      'longitude'       => new sfWidgetFormInputText(),
      'categories_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'description'     => new sfValidatorString(array('required' => false)),
      'partita_iva'     => new sfValidatorString(array('max_length' => 255)),
      'city'            => new sfValidatorString(array('max_length' => 25)),
      'address'         => new sfValidatorString(array('max_length' => 255)),
      'state'           => new sfValidatorString(array('max_length' => 25)),
      'zipcode'         => new sfValidatorString(array('max_length' => 255)),
      'phone_number'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mobile_number'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'fax_number'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email'           => new sfValidatorString(array('max_length' => 255)),
      'logo'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'website'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'slug'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'latitude'        => new sfValidatorPass(array('required' => false)),
      'longitude'       => new sfValidatorPass(array('required' => false)),
      'categories_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Company', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('company[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Company';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['categories_list']))
    {
      $this->setDefault('categories_list', $this->object->Categories->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveCategoriesList($con);

    parent::doSave($con);
  }

  public function saveCategoriesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['categories_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Categories->getPrimaryKeys();
    $values = $this->getValue('categories_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Categories', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Categories', array_values($link));
    }
  }

}
