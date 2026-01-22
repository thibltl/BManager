<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    /**
     * Retourne toutes les tâches d’un projet.
     * ⚠️ Le contrôleur DOIT vérifier l'accès utilisateur.
     */
    public function findByProject(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->distinct()
            ->andWhere('t.task_project = :project')
            ->setParameter('project', $project)
            ->orderBy('t.task_dueDate', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les tâches d’un projet visibles par un utilisateur.
     * (assignées OU non assignées)
     */
    public function findForUserInProject(User $user, Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->distinct()
            ->leftJoin('t.users', 'u')
            ->andWhere('t.task_project = :project')
            ->andWhere('u = :user OR u IS NULL')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->orderBy('t.task_dueDate', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les tâches des projets dont l'utilisateur est membre.
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->distinct()
            ->join('t.task_project', 'p')
            ->join('p.users', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('t.task_dueDate', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
