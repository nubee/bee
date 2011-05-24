<?php

/**
 * Company form.
 *
 * @package    findeal
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CompanyForm extends BaseCompanyForm
{
  public function configure()
  {
    unset(
      $this['created_at'], $this['updated_at'], $this['latitude'], $this['longitude']
    );

    $choices = $this->getWidget('categories_list')->getChoices();
    $this->setWidget('categories_list', new sfWidgetFormSelectCheckbox(array('choices' => $choices)));
    
    $this->setWidget('website', new sfWidgetFormInputText(array('default' => 'http://')));
    $this->setValidator('email', new sfValidatorEmail(array('trim' => true), array('invalid' => 'Please enter a valid e-mail address (anemail@example.com)')));
    $this->setValidator('website', new sfValidatorUrl(array(), array('invalid' => 'Please enter a valid web address (http://websiteexample.com)')));
    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare('phone_number', sfValidatorSchemaCompare::NOT_EQUAL, 'mobile_number', array(), array('invalid' => 'At least one number is required'))
      );
  }
}
