<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tasks>
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    /**
     * Retourne toutes les tâches d’un projet.
     *
     * @return Tasks[]
     */
    public function findByProject(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.task_project = :project')
            ->setParameter('project', $project)
            ->orderBy('t.task_dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les tâches d’un projet visibles par un utilisateur.
     * (Optionnel mais utile si tu veux filtrer par assignation)
     *
     * @return Tasks[]
     */
    public function findForUserInProject(User $user, Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.users', 'u')
            ->andWhere('t.task_project = :project')
            ->andWhere('u = :user OR u IS NULL')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->orderBy('t.task_dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
