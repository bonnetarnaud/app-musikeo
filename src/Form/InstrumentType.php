<?php

namespace App\Form;

use App\Entity\Instrument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstrumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'instrument',
                'choices' => [
                    'Piano/Clavier' => 'clavier',
                    'Guitare' => 'cordes',
                    'Violon' => 'cordes',
                    'Violoncelle' => 'cordes',
                    'Contrebasse' => 'cordes',
                    'Flûte' => 'vents',
                    'Clarinette' => 'vents',
                    'Saxophone' => 'vents',
                    'Trompette' => 'vents',
                    'Trombone' => 'vents',
                    'Batterie' => 'percussions',
                    'Djembé' => 'percussions',
                    'Xylophone' => 'percussions',
                ],
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-textarea rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'rows' => 3,
                    'placeholder' => 'Description détaillée de l\'instrument...'
                ]
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => [
                    'class' => 'form-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Ex: Yamaha, Fender...'
                ]
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'class' => 'form-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Ex: P-125, Stratocaster...'
                ]
            ])
            ->add('serialNumber', TextType::class, [
                'label' => 'Numéro de série',
                'attr' => [
                    'class' => 'form-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Numéro de série unique'
                ]
            ])
            ->add('condition', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Excellent' => 'excellent',
                    'Bon' => 'good',
                    'Correct' => 'fair',
                    'Mauvais' => 'poor',
                    'Réparation nécessaire' => 'needs_repair',
                ],
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('isRentable', CheckboxType::class, [
                'label' => 'Disponible à la location',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                ]
            ])
            ->add('additionalInfo', TextareaType::class, [
                'label' => 'Informations additionnelles',
                'required' => false,
                'attr' => [
                    'class' => 'form-textarea rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'rows' => 3,
                    'placeholder' => 'Accessoires inclus, particularités, historique...'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instrument::class,
        ]);
    }
}
