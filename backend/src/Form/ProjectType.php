<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project_name', null, [
                'label' => 'Nom du projet',
            ])
            ->add('project_desc', null, [
                'label' => 'Description',
            ])

            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getName() ?: $user->getEmail(),
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label' => 'Membres du projet',

                // 🔥 Filtrage des utilisateurs autorisés
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
                        ->setParameter('role', '"ROLE_USER"')
                        ->orderBy('u.name', 'ASC');
                },

                // 🔥 Important pour ManyToMany
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
