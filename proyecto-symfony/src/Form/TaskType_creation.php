<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TaskType_creation extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title',TextType::class,[
                     'label'=> 'Nombre de la tarea:'
                 ])
                ->add('content',TextareaType::class,[
                     'label'=>'Contenido de la tarea:'
                 ])
                ->add('priority',ChoiceType::class,[
                    'label'=>'Prioridad de la tarea:',
                    'choices'=>['Alta'=>'high','Media'=>'medium','Baja'=>'low']
                 ])
                ->add('fechafin',TextType::class,[
                    'label'=>'Fecha para la que quieres tener la tarea acabada:',
                    'attr'=>['class'=>'calendario']
                    
                 ])
                ->add('hours',IntegerType::class,[
                    'label'=>'Horas presupuestadas:'
                ])
                
                ->add('submit',SubmitType::class,[
                   'label'=>'Guardar',
                    'attr'=>['class'=>'btn']
                ]);
    }
    
    
    
}




