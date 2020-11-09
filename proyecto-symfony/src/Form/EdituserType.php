<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EdituserType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name',TextType::class,[
                     'label'=> 'Nombre Usuario:'
                 ])
                ->add('surname',TextType::class,[
                     'label'=>'Apellidos Usuario:'
                 ])
                ->add('email',EmailType::class,[
                    'label'=>'Correo Electrónico'
                 ])
                ->add('enterprise',TextType::class,[
                    'label'=>'Nombre de la empresa a la que perteneces(si te quieres registrar sin empresa deja el campo en blanco)'
                ])
                
                ->add('submit',SubmitType::class,[
                   'label'=>'MODIFICAR',
                    'attr'=>['class'=>'btn']
                ]);
    }
    
    
    
}


