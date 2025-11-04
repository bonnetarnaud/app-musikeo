<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle',
                'attr' => [
                    'placeholder' => 'Ex: Salle de piano A1',
                    'maxlength' => 100
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de la salle est obligatoire.'
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de la salle ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité (nombre de personnes)',
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                    'placeholder' => 'Ex: 8'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La capacité de la salle est obligatoire.'
                    ]),
                    new Assert\Positive([
                        'message' => 'La capacité doit être un nombre positif.'
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                        'notInRangeMessage' => 'La capacité doit être entre {{ min }} et {{ max }} personnes.'
                    ])
                ]
            ])
            ->add('location', TextareaType::class, [
                'label' => 'Localisation / Description',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex: Bâtiment A, 1er étage, porte 101\nÉquipée d\'un piano droit Yamaha',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}