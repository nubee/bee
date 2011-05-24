<?php

/**
 * BaseCompanyCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $category_id
 * @property integer $company_id
 * @property Company $Company
 * @property Category $Category
 * 
 * @method integer         getCategoryId()  Returns the current record's "category_id" value
 * @method integer         getCompanyId()   Returns the current record's "company_id" value
 * @method Company         getCompany()     Returns the current record's "Company" value
 * @method Category        getCategory()    Returns the current record's "Category" value
 * @method CompanyCategory setCategoryId()  Sets the current record's "category_id" value
 * @method CompanyCategory setCompanyId()   Sets the current record's "company_id" value
 * @method CompanyCategory setCompany()     Sets the current record's "Company" value
 * @method CompanyCategory setCategory()    Sets the current record's "Category" value
 * 
 * @package    findeal
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseCompanyCategory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('company_category');
        $this->hasColumn('category_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('company_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Company', array(
             'local' => 'company_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Category', array(
             'local' => 'category_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}