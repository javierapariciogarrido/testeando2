<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegisterType extends AbstractType{
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
                ->add('password',PasswordType::class,[
                    'label'=>'Contraseña:'
                ])
                ->add('fraseseguridad',TextType::class,[
                    'label'=>'Introduce una frase de seguridad por si olvidas o cambias tu contraseña:'
                ])
                ->add('enterprise',TextType::class,[
                    'label'=>'Nombre de la empresa a la que perteneces(si te quieres registrar sin empresa deja el campo en blanco)',
                    'attr'=>['required'=>'false']
                ])
                ->add('submit',SubmitType::class,[
                   'label'=>'Guardar Datos',
                    'attr'=>['class'=>'btn']
                ]);
    }
    
    
    
}

