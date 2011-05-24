<?php

/**
 * BaseCompany
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property string $partita_iva
 * @property string $city
 * @property string $address
 * @property string $state
 * @property string $zipcode
 * @property string $phone_number
 * @property string $mobile_number
 * @property string $fax_number
 * @property string $email
 * @property string $logo
 * @property string $website
 * @property sfGuardUser $User
 * @property Doctrine_Collection $Categories
 * @property Doctrine_Collection $CompanyCategory
 * @property Doctrine_Collection $Deals
 * @property Doctrine_Collection $FavoriteCompanies
 * 
 * @method integer             getUserId()            Returns the current record's "user_id" value
 * @method string              getName()              Returns the current record's "name" value
 * @method string              getDescription()       Returns the current record's "description" value
 * @method string              getPartitaIva()        Returns the current record's "partita_iva" value
 * @method string              getCity()              Returns the current record's "city" value
 * @method string              getAddress()           Returns the current record's "address" value
 * @method string              getState()             Returns the current record's "state" value
 * @method string              getZipcode()           Returns the current record's "zipcode" value
 * @method string              getPhoneNumber()       Returns the current record's "phone_number" value
 * @method string              getMobileNumber()      Returns the current record's "mobile_number" value
 * @method string              getFaxNumber()         Returns the current record's "fax_number" value
 * @method string              getEmail()             Returns the current record's "email" value
 * @method string              getLogo()              Returns the current record's "logo" value
 * @method string              getWebsite()           Returns the current record's "website" value
 * @method sfGuardUser         getUser()              Returns the current record's "User" value
 * @method Doctrine_Collection getCategories()        Returns the current record's "Categories" collection
 * @method Doctrine_Collection getCompanyCategory()   Returns the current record's "CompanyCategory" collection
 * @method Doctrine_Collection getDeals()             Returns the current record's "Deals" collection
 * @method Doctrine_Collection getFavoriteCompanies() Returns the current record's "FavoriteCompanies" collection
 * @method Company             setUserId()            Sets the current record's "user_id" value
 * @method Company             setName()              Sets the current record's "name" value
 * @method Company             setDescription()       Sets the current record's "description" value
 * @method Company             setPartitaIva()        Sets the current record's "partita_iva" value
 * @method Company             setCity()              Sets the current record's "city" value
 * @method Company             setAddress()           Sets the current record's "address" value
 * @method Company             setState()             Sets the current record's "state" value
 * @method Company             setZipcode()           Sets the current record's "zipcode" value
 * @method Company             setPhoneNumber()       Sets the current record's "phone_number" value
 * @method Company             setMobileNumber()      Sets the current record's "mobile_number" value
 * @method Company             setFaxNumber()         Sets the current record's "fax_number" value
 * @method Company             setEmail()             Sets the current record's "email" value
 * @method Company             setLogo()              Sets the current record's "logo" value
 * @method Company             setWebsite()           Sets the current record's "website" value
 * @method Company             setUser()              Sets the current record's "User" value
 * @method Company             setCategories()        Sets the current record's "Categories" collection
 * @method Company             setCompanyCategory()   Sets the current record's "CompanyCategory" collection
 * @method Company             setDeals()             Sets the current record's "Deals" collection
 * @method Company             setFavoriteCompanies() Sets the current record's "FavoriteCompanies" collection
 * 
 * @package    findeal
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseCompany extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('company');
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('partita_iva', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('city', 'string', 25, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('address', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('state', 'string', 25, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('zipcode', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('phone_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('mobile_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('fax_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('logo', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('website', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id'));

        $this->hasMany('Category as Categories', array(
             'refClass' => 'CompanyCategory',
             'local' => 'company_id',
             'foreign' => 'category_id'));

        $this->hasMany('CompanyCategory', array(
             'local' => 'id',
             'foreign' => 'company_id'));

        $this->hasMany('Deal as Deals', array(
             'local' => 'id',
             'foreign' => 'company_id'));

        $this->hasMany('FavoriteCompany as FavoriteCompanies', array(
             'local' => 'id',
             'foreign' => 'company_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $sluggable0 = new Doctrine_Template_Sluggable(array(
             'fields' => 
             array(
              0 => 'name',
             ),
             ));
        $geographical0 = new Doctrine_Template_Geographical();
        $this->actAs($timestampable0);
        $this->actAs($sluggable0);
        $this->actAs($geographical0);
    }
}