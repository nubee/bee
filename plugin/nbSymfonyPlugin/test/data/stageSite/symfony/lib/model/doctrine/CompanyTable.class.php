<?php

class CompanyTable extends Doctrine_Table
{
  public static function getInstance()
  {
    return Doctrine_Core::getTable('Company');
  }

  public function getDealsByUserId($user_id) {
     $q = self::getInstance()->createQuery()
        ->from('Deal d')
        ->innerJoin('d.Company c')
        ->where('c.user_id = ?', $user_id);

     return $q->execute();
  }
}