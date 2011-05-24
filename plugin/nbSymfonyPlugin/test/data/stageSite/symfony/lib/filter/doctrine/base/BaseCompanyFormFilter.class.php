<?php

/**
 * Company filter form base class.
 *
 * @package    findeal
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCompanyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'     => new sfWidgetFormFilterInput(),
      'partita_iva'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'city'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'address'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'state'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'zipcode'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'phone_number'    => new sfWidgetFormFilterInput(),
      'mobile_number'   => new sfWidgetFormFilterInput(),
      'fax_number'      => new sfWidgetFormFilterInput(),
      'email'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'logo'            => new sfWidgetFormFilterInput(),
      'website'         => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'slug'            => new sfWidgetFormFilterInput(),
      'latitude'        => new sfWidgetFormFilterInput(),
      'longitude'       => new sfWidgetFormFilterInput(),
      'categories_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'user_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'name'            => new sfValidatorPass(array('required' => false)),
      'description'     => new sfValidatorPass(array('required' => false)),
      'partita_iva'     => new sfValidatorPass(array('required' => false)),
      'city'            => new sfValidatorPass(array('required' => false)),
      'address'         => new sfValidatorPass(array('required' => false)),
      'state'           => new sfValidatorPass(array('required' => false)),
      'zipcode'         => new sfValidatorPass(array('required' => false)),
      'phone_number'    => new sfValidatorPass(array('required' => false)),
      'mobile_number'   => new sfValidatorPass(array('required' => false)),
      'fax_number'      => new sfValidatorPass(array('required' => false)),
      'email'           => new sfValidatorPass(array('required' => false)),
      'logo'            => new sfValidatorPass(array('required' => false)),
      'website'         => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'slug'            => new sfValidatorPass(array('required' => false)),
      'latitude'        => new sfValidatorPass(array('required' => false)),
      'longitude'       => new sfValidatorPass(array('required' => false)),
      'categories_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addCategoriesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.CompanyCategory CompanyCategory')
      ->andWhereIn('CompanyCategory.category_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Company';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'user_id'         => 'ForeignKey',
      'name'            => 'Text',
      'description'     => 'Text',
      'partita_iva'     => 'Text',
      'city'            => 'Text',
      'address'         => 'Text',
      'state'           => 'Text',
      'zipcode'         => 'Text',
      'phone_number'    => 'Text',
      'mobile_number'   => 'Text',
      'fax_number'      => 'Text',
      'email'           => 'Text',
      'logo'            => 'Text',
      'website'         => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'slug'            => 'Text',
      'latitude'        => 'Text',
      'longitude'       => 'Text',
      'categories_list' => 'ManyKey',
    );
  }
}
