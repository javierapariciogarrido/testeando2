<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
    // CONTROLADOR DE LA AYUDA EN GENERAL 
    public function index()
    {
      return $this->render('help/index.html.twig');
    }

    // CONTROLADOR DEL SOBRE LA APP
    public function about(){
        return $this->render('help/about.html.twig');
    }
    // CONTROLADOR SOBRE EL AVISO LEGAL 
    public function AvisoLegal(){
        return $this->render('help/avisolegal.html.twig');
    }

    // CONTROLADOR SOBRE LA POLITICA DE PRIVACIDAD
    public function PoliticaPrivacidad(){
        return $this->render('help/politicaprivacidad.html.twig');
    }

    
}
