<?php

namespace Wotoog\BlogBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * BlogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlogRepository extends EntityRepository
{
    /**
     * Return the author of the blog
     * @param Blog $blog
     *
     * @return null|object
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function getOwner(\Wotoog\BlogBundle\Entity\Blog $blog){
        $user = $this->getEntityManager()->getRepository('WotoogUserBundle:User')->findOneBy(array('blog' => $blog));
        if($user)
            return $user;
        $club = $this->getEntityManager()->getRepository('WotoogClubBundle:Club')->findOneBy(array('blog' => $blog));
        if($club)
            return $club;
        throw new EntityNotFoundException();
    }

    public function hasAdminRights(\Wotoog\UserBundle\Entity\User $user, \Wotoog\BlogBundle\Entity\Blog $blog){
        $owner = $this->getOwner($blog);
        if(get_class($owner) == 'Wotoog\ClubBundle\Entity\Club'){
            $clubs = $user->getClubs();
            foreach ($clubs as $club) {
                if($owner == $club)
                    return true;
            }
            return false;
        }else{
            if ($owner == $user)
                return true;
        }
    }

    function getCategories(\Wotoog\BlogBundle\Entity\Blog $blog){
        $categories = array();
        $result = $this->_em->createQueryBuilder()
            ->select('post.category')
            ->distinct(true)
            ->from('\Wotoog\BlogBundle\Entity\Post', 'post')
            ->where('post.blog =:blog')
            ->setParameter('blog', $blog)
            ->getQuery()->getScalarResult();
        if($result){
            foreach ($result as $record) {
                $categories[] = $record['category'];
            }
            return $categories;
        }
        return array();
    }
}
