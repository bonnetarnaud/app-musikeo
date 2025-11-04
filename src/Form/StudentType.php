<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'exemple@email.com',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse email est obligatoire',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse email valide',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom de l\'élève',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est obligatoire',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'attr' => [
                    'placeholder' => 'Nom de famille de l\'élève',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de famille est obligatoire',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de famille ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-input'
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '01 23 45 67 89',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse complète de l\'élève...',
                    'rows' => 3,
                    'class' => 'form-textarea'
                ],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => $isEdit ? 'Nouveau mot de passe (laisser vide pour conserver l\'actuel)' : 'Mot de passe',
                'required' => !$isEdit,
                'mapped' => false,
                'attr' => [
                    'placeholder' => $isEdit ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe de connexion',
                    'class' => 'form-input'
                ],
                'constraints' => $isEdit ? [] : [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
            'is_edit' => false,
        ]);
    }
}