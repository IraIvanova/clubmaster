<?php

namespace Club\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Group
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Group extends EntityRepository
{
  public function findAll()
  {
    $res = parent::findAll();

    $groups = array();
    foreach ($res as $group) {
      if ($group->getGroupType() == 'dynamic') {
        $query = $this->getDynamicQuery($group);

        $users = $query->getQuery()->getResult();
        $group->setUsers($users);
      }

      $groups[] = $group;
    }

    return $groups;
  }

  public function getDynamicQuery(\Club\UserBundle\Entity\Group $group)
  {
    $query = $this->_em->createQueryBuilder()
      ->select('u')
      ->from('\Club\UserBundle\Entity\User','u')
      ->leftJoin('u.profile','p');

    if ($group->getGender()) {
      $query
        ->andWhere('p.gender = ?1')
        ->setParameter(1, $group->getGender());
    }

    if ($group->getMinAge()) {
      $query
        ->andWhere('p.day_of_birth <= ?2')
        ->setParameter(2, date('Y-m-d',mktime(0,0,0,date('n'),date('j'),date('Y')-$group->getMinAge())));
    }

    if ($group->getMaxAge()) {
      $query
        ->andWhere('p.day_of_birth >= ?3')
        ->setParameter(3, date('Y-m-d',mktime(0,0,0,date('n'),date('j'),date('Y')-$group->getMaxAge())));
    }

    if ($group->getIsActiveMember()) {
      $query
        ->leftJoin('u.subscriptions','s')
        ->leftJoin('u.ticket_coupons','t')
        ->andWhere('s.expire_date >= ?4')
        ->orWhere('t.expire_date >= ?5 AND t.ticket > ?6')
        ->setParameter(4,date('Y-m-d'))
        ->setParameter(5,date('Y-m-d'))
        ->setParameter(6,0);
    }

    if (count($group->getLocation()) > 0) {
      foreach ($group->getLocation() as $location) {
        $query
          ->leftJoin('u.subscriptions','s2')
          ->leftJoin('s2.locations','l')
          ->andWhere('s2.expire_date >= ?7')
          ->andWhere('l.id = ?8')
          ->setParameter(7,date('Y-m-d'))
          ->setParameter(8,$location->getId());
      }
    }

    return $query;
  }
}
