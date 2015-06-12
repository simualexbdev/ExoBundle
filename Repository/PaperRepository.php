<?php

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PaperRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaperRepository extends EntityRepository
{
    /**
     * Get a student's Paper which is not finished
     *
     * @access public
     *
     * @param integer $userID id User
     * @param integer $exerciseID id Exercise
     *
     * Return array[Paper]
     */
    public function getPaper($userID, $exerciseID)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.exercise', 'e')
            ->where($qb->expr()->in('u.id', $userID))
            ->andWhere($qb->expr()->in('e.id', $exerciseID))
            ->andWhere('p.end IS NULL');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the user's papers for an exercise
     *
     * @access public
     *
     * @param integer $userID id User
     * @param integer $exerciseID id Exercise
     * @param boolean $finished to return or no the papers no finished
     *
     * Return array[Paper]
     */
    public function getExerciseUserPapers($userID, $exerciseID, $finished = false)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.exercise', 'e')
            ->where($qb->expr()->in('u.id', $userID))
            ->andWhere($qb->expr()->in('e.id', $exerciseID))
            ->orderBy('p.id', 'ASC');

        if ($finished === true) {
            $qb->andWhere('p.end is NOT NULL');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all papers for an exercise
     *
     * @access public
     *
     * @param integer $exerciseID id Exercise
     *
     * Return array[Paper]
     */
    public function getExerciseAllPapers($exerciseID)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.exercise', 'e')
            ->join('p.user', 'u')
            ->where($qb->expr()->in('e.id', $exerciseID))
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->addOrderBy('p.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns all papers for an exercise for CSV export
     *
     * @access public
     *
     * @param integer $exerciseID id Exercise
     *
     * Return array[Paper]
     */
    public function getExerciseAllPapersIterator($exerciseID)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.exercise', 'e')
            ->join('p.user', 'u')
            ->where($qb->expr()->in('e.id', $exerciseID))
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->addOrderBy('p.id', 'ASC');

        return $qb->getQuery()->iterate();
    }

    /**
     * Returns all papers of all exercise for an user
     *
     * @access public
     *
     * @param integer $userID id User
     *
     * Return array[Paper]
     */
    public function getPaperUser($userID)
    {
        $dql = 'SELECT p FROM UJM\ExoBundle\Entity\Paper p
                WHERE p.user = ?1';

        $query = $this->_em->createQuery($dql)->setParameter(1, $userID);

        return $query->getResult();
    }

    /**
     * Returns number of papers for an exercise
     *
     * @access public
     *
     * @param integer $exerciseID id Exercise
     *
     * Return integer
     */
    public function countPapers($exerciseID)
    {
        $qb = $this->createQueryBuilder('p');

        $nbPapers = $qb->select('COUNT(p)')
                       ->join('p.exercise', 'e')
                       ->join('p.user', 'u')
                       ->where($qb->expr()->in('e.id', $exerciseID))
                       ->getQuery()
                       ->getSingleScalarResult();

        return $nbPapers;
    }
}