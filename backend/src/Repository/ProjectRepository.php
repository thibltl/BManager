<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Retourne uniquement les projets dont l'utilisateur est membre.
     *
     * @return Project[]
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.users', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('p.project_createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
