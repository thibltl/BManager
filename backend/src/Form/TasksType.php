<?php

namespace App\Form;

use App\Entity\Priority;
use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Tasks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task_title')
            ->add('task_description')
            ->add('task_due_date')
            ->add('task_create_at')
            ->add('task_lastchange')
            ->add('task_priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'id',
            ])
            ->add('task_status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'id',
            ])
            ->add('task_project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
