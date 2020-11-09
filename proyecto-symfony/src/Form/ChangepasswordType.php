<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ChangepasswordType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('password',PasswordType::class,[
                    'label'=>'Nueva Contraseña:'
                ])
                ->add('fraseseguridad',TextType::class,[
                    'label'=>'Introduce tu clave de Seguridad Para Poder Cambiar tu contraseña:' 
                ])
                ->add('submit',SubmitType::class,[
                   'label'=>'CAMBIAR CONTRASEÑA',
                    'attr'=>['class'=>'btn']
                ]);
    }
    
    
    
}


