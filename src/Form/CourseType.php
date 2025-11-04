<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $organization = $options['organization'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du cours',
                'attr' => [
                    'placeholder' => 'Ex: Piano débutant, Guitare intermédiaire...',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du cours est obligatoire',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez le contenu et les objectifs du cours...',
                    'rows' => 4,
                    'class' => 'form-textarea'
                ],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
                'choice_label' => function (Teacher $teacher) {
                    return $teacher->getFullName() . ' (' . $teacher->getEmail() . ')';
                },
                'placeholder' => 'Sélectionnez un professeur',
                'label' => 'Professeur',
                'attr' => [
                    'class' => 'form-select'
                ],
                'query_builder' => function ($repository) use ($organization) {
                    return $repository->createQueryBuilder('t')
                        ->where('t.organization = :organization')
                        ->setParameter('organization', $organization)
                        ->orderBy('t.lastname', 'ASC')
                        ->addOrderBy('t.firstname', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un professeur',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
            'organization' => null,
        ]);

        $resolver->setRequired('organization');
    }
}