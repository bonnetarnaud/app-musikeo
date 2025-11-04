<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom du professeur'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est obligatoire',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => [
                    'placeholder' => 'Nom de famille du professeur'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de famille est obligatoire',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'placeholder' => 'email@exemple.com'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse e-mail est obligatoire',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer une adresse e-mail valide',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '01 23 45 67 89'
                ],
            ])
            ->add('specialties', TextType::class, [
                'label' => 'Spécialités',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Piano, Violon, Guitare...'
                ],
                'help' => 'Séparez les spécialités par des virgules'
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie / Présentation',
                'property_path' => 'biography',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Présentation du professeur, formation, expérience...'
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $options['is_edit'] ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => $options['is_edit'] ? 'Laissez vide pour ne pas changer' : 'Mot de passe sécurisé'
                ],
                'constraints' => $options['is_edit'] ? [] : [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
                'help' => $options['is_edit'] ? 'Laissez vide pour conserver le mot de passe actuel' : 'Minimum 6 caractères'
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'mapped' => false,
                'choices' => [
                    'Actif' => 'active',
                    'Inactif' => 'inactive',
                ],
                'data' => 'active',
                'help' => 'Un professeur actif peut se connecter et enseigner'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
            'is_edit' => false,
        ]);
    }
}