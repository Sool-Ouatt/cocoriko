<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephone', TelType::class)
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('date',DateType::class,['mapped' => false])
            ->add('Adresse',TextType::class,['mapped' => false])
          /*  ->add('ModePayement', ChoiceType::class, [
                'choices' => [
                    'En ligne' => 'Enligne',
                    'A la livraison' => 'Livraison',
                    'A la boutique' => 'Magasin', ],
                'mapped'  => false])
          */
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
