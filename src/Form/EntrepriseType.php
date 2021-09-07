<?php

namespace App\Form;

use App\Entity\Entreprise;
use Doctrine\DBAL\Types\BigIntType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idEntreprise')
            ->add('ville')
            ->add('quartier')
            ->add('rue')
            ->add('porte')
            ->add('telephoneEntreprise',TelType::class,['mapped' => false])
            ->add('telephoneResponsable',TelType::class)
            ->add('nom',\Symfony\Component\Form\Extension\Core\Type\TextType::class,['mapped' => false])
            ->add('prenom',\Symfony\Component\Form\Extension\Core\Type\TextType::class,['mapped' => false])
            ->add('email',EmailType::class,['mapped' => false])
            ->add('username',\Symfony\Component\Form\Extension\Core\Type\TextType::class,['mapped' => false])
            ->add('motDePasse',\Symfony\Component\Form\Extension\Core\Type\TextType::class,['mapped' => false])
            ->add('Enregistrer', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
