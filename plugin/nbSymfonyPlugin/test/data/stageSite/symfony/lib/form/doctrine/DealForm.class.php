<?php

/**
 * Deal form.
 *
 * @package    findeal
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DealForm extends BaseDealForm
{
  public function configure()
  {
    unset(
      $this['created_at'], $this['updated_at']
    );
    $this->setWidget('published_at', new sfWidgetFormJQueryDate(array('config'=>'{}')));
    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(
        array(
          new sfValidatorSchemaCompare('published_at', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'start_at',
            array('throw_global_error' => true),
            array('invalid' => 'Published at ("%left_field%") must be before start at ("%right_field%")')),
          new sfValidatorSchemaCompare('start_at', sfValidatorSchemaCompare::LESS_THAN, 'end_at',
            array('throw_global_error' => true),
            array('invalid' => 'Start at ("%left_field%") must be before end at ("%right_field%")')),
          new sfValidatorSchemaCompare('price', sfValidatorSchemaCompare::GREATER_THAN, 'discount',
            array('throw_global_error' => true),
            array('invalid' => 'Price ("%left_field%") must be upper or equal than discount ("%right_field%")'))
      )));
  }
}
