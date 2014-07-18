<?php

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * QuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuestionRepository extends EntityRepository
{
    /**
     * teatcher's Questions
     *
     */
    public function getQuestionsUser($userId)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->join('q.user', 'u')
            ->where($qb->expr()->in('u.id', $userId));

        return $qb->getQuery()->getResult();
    }

    /**
     * Allow to know if the User is the owner of this Question
     *
     */
    public function getControlOwnerQuestion($user, $question)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->join('q.user', 'u')
            ->where($qb->expr()->in('q.id', $question))
            ->andWhere($qb->expr()->in('u.id', $user));

        return $qb->getQuery()->getResult();
    }

    public function findByCategory($userId, $whatToFind)
    {
        $dql = 'SELECT q FROM UJM\ExoBundle\Entity\Question q JOIN q.category c
            WHERE c.value LIKE :search
            AND q.user = '.$userId.'
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }

    public function findByTitle($userId, $whatToFind)
    {
        $dql = 'SELECT q FROM UJM\ExoBundle\Entity\Question q
            WHERE q.title LIKE :search
            AND q.user = '.$userId.'
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }
}