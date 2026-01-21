<?php

namespace App\Form;

use App\Entity\Priority;
use App\Entity\Status;
use App\Entity\Tasks;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $projectUsers = $options['project_users'];

        $builder
            ->add('task_title', null, [
                'label' => 'Titre de la tâche',
                'required' => true,
            ])

            ->add('task_desc', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])

            ->add('task_dueDate', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'required' => false,
            ])

            ->add('task_priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'priorityName',
                'label' => 'Priorité',
                'placeholder' => 'Choisir une priorité',
                'required' => true,
            ])

            ->add('task_status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'statusName',
                'label' => 'Statut',
                'placeholder' => 'Choisir un statut',
                'required' => true,
            ])

            ->add('users', EntityType::class, [
                'class' => User::class,
                'choices' => $projectUsers,
                'choice_label' => fn(User $user) => $user->getName() ?: $user->getEmail(),
                'label' => 'Assigner à',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'multiple' => 'multiple',
                    'size' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
            'project_users' => [],
        ]);

        $resolver->setAllowedTypes('project_users', ['array']);
    }
}
