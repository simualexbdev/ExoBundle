<?php

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ExerciseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExerciseRepository extends EntityRepository
{
    public function getExerciseMarks($exoId, $order = '')
    {
        if ($order != '') {
            $orderBy = ' ORDER BY '.$order;
        }
        $dql = 'SELECT sum(r.mark) as noteExo, p.id as paper
            FROM UJM\ExoBundle\Entity\Response r JOIN r.paper p JOIN p.exercise e
            WHERE e.id='.$exoId.' AND p.interupt=0 group by p.id'.$orderBy;

        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function getExerciceByUser($userID)
    {
        $dql = 'SELECT e.id, e.title
            FROM UJM\ExoBundle\Entity\Subscription s JOIN s.exercise e
            WHERE s.user='.$userID.' AND s.creator = 1';

        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function getExerciseAdmin($userID)
    {
        $exercises = array();

        $dql = 'SELECT w.id, w.name
            FROM Claroline\CoreBundle\Entity\User u
            JOIN u.roles r
            JOIN r.workspace w
            WHERE u.id='.$userID.' AND r.name LIKE \'ROLE_WS_MANAGER_%\'
            ORDER BY w.name' ;

        $query = $this->_em->createQuery($dql);

        foreach ($query->getResult() as $ws) {
            $dql = 'SELECT e.id, e.title, w.name
                    FROM UJM\ExoBundle\Entity\Exercise e
                    JOIN e.resourceNode rn
                    JOIN rn.resourceType rt
                    JOIN rn.workspace w
                    WHERE rt.name =\'ujm_exercise\'
                    AND w.id='.$ws['id'].'
                    ORDER BY e.title';
            $queryResources = $this->_em->createQuery($dql);
            foreach ($queryResources->getResult() as $resource) {
                $exercises[] =  $resource;
            }
        }

        /*$dql = "
            SELECT e.id, e.title
            FROM UJM\ExoBundle\Entity\Exercise e
            JOIN e.resourceNode node
            JOIN node.rights right
            JOIN right.role r
            JOIN node.resourceType rt
            WHERE r.name IN (
                SELECT r2.name FROM Claroline\CoreBundle\Entity\Role r2
                LEFT JOIN r2.users u2
                LEFT JOIN r2.groups g
                LEFT JOIN g.users u3
                WHERE r2.name LIKE 'ROLE_WS_MANAGER_%'
                AND u2.id = '$userID'
                OR u3.id = '$userID'
                AND r2.name LIKE 'ROLE_WS_MANAGER_%'
            ) AND rt.name ='ujm_exercise'
        ";

        $queryResources = $this->_em->createQuery($dql);
            foreach ($queryResources->getResult() as $resource) {
                $exercises[] =  $resource;
            }*/

        return $exercises;

    }
}