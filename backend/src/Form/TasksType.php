<?php

namespace App\Form;

use App\Entity\Priority;
use App\Entity\Project;
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
        $projectUsers = $options['project_users']; // injecté depuis le controller

        $builder
            ->add('task_title', null, [
                'label' => 'Titre de la tâche',
            ])

            ->add('task_desc', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4],
            ])

            ->add('task_dueDate', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
            ])

            ->add('task_priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'name', // beaucoup mieux que id
                'label' => 'Priorité',
                'placeholder' => 'Choisir une priorité',
            ])

            ->add('task_status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label' => 'Statut',
                'placeholder' => 'Choisir un statut',
            ])

            ->add('task_project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'projectName',
                'label' => 'Projet',
                'placeholder' => 'Sélectionner un projet',
            ])

            ->add('users', EntityType::class, [
                'class' => User::class,
                'choices' => $projectUsers, // uniquement les membres du projet
                'choice_label' => 'name',
                'label' => 'Assigner à',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
            'project_users' => [], // paramètre custom
        ]);
    }
}
