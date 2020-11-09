<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request; // Para recibir por post
use App\Form\RegisterType;  // Modelo de Formulario de Registro
use App\Form\EdituserType; // Modelo de Formulario de Editar datos de Usuario
use App\Form\UserresetpasswordType; //Modelo Formulario Restablecer contraseña olvidada
use App\Form\ChangepasswordType; //Modelo de Formulario cambiar contraseña
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Encriptar password
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; //Libreria para logado
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session; // Importo objeto para crear sesion
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;






class UserController extends AbstractController {
    
    public function register(Request $request, UserPasswordEncoderInterface $encoder) {
        // Instanciamos modelo usuario
        $user = new User();

        //Creamos el formulario
        $form = $this->createForm(RegisterType::class, $user);

        //Vinculamos(seteando) lo que llega por post(request) con modelo user
        $form->handleRequest($request);

        //Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isvalid()) {
            // Comprobar si el email el cual nos queremos registrar existe ya en la base de datos
            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $emailusuario = $user_repo->findBy(['email' => $user->getEmail()]);


            // Aqui comprobamos si no existe otro usuario que tenga el mismo email
            $coincidencias = count($emailusuario);

            if ($coincidencias == 0) {
                //Setemamos campo Role manualmente:
                $user->setRole('ROLE_USER');

                //Seteamos manualmente la fecha de registro del usuario a la de hoy
                $user->setCreatedAt(new \Datetime('now'));

                //Cifrar contraseña
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                // Seteo la contraseña cifrada

                $user->setPassword($encoded);

                //Ciframos la frase de seguridad
                $frasencoded = $encoder->encodePassword($user, $user->getFraseseguridad());
                
                $user->setFraseseguridad($frasencoded);



                //Guardamos usuario ya que todos los campos de usuario estan rellenos
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // Creamos sesión flash informando que el usuario ha sifo registrado
                $session = new Session();
                $session->getFlashBag()->add('message', '¡¡USUARIO REGISTRADO CORRECTAMENTE,YA PUEDES INICIAR SESION!!');
            } else { // Si existe el email que metemos para registrarnos nos avisa que no podemos
                $session = new Session();
                $session->getFlashBag()->add('error', '¡¡NO PUEDES REGISTRARTE CON ESE EMAIL PORQUE OTRO USUARIO YA LO TIENE!!');
            }




            //Redirigimos
            return $this->redirectToRoute('inicio');
        }// Fin if

        return $this->render('user/register.html.twig', [
                    'form' => $form->createView()
        ]);
    }

// Fin metodo register 

    public function login(AuthenticationUtils $autenticationUtils) {
        $error = $autenticationUtils->getLastAuthenticationError();
        $lastUsername = $autenticationUtils->getLastUsername();
        
        
        
        return $this->render('user/login.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername
        ]);
    }
    // Buscamos cliente elegido y mostramos sus datos 
    public function aboutme($id) {

        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $user = $user_repo->find($id);

        return $this->render('user/detail_user.html.twig', [
                    'user' => $user
        ]);
    }

    public function edit(Request $request, $id) {
        //Creo variable para saber que he entrado en metodo edición usuario y reutilizar la vista
        //register.html.twig cambiando solo el title y el h2
        $edicion_usuario = true;
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $user = $user_repo->find($id);

        //Obtengo el token si hay un usuario conectado para recuperar al usuario
        //$user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
        //Creamos el formulario
        $form = $this->createForm(EdituserType::class, $user);

        //Vinculamos(seteando) lo que llega por post(request) con modelo user
        $form->handleRequest($request);

        //Comprobamos si el formulario se ha enviado
        if ($form->isSubmitted() && $form->isvalid()) {



            //Guardamos usuario ya que todos los campos de usuario estan rellenos
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Creamos sesión flash informando que el usuario ha sifo registrado
            $session = new Session();
            $session->getFlashBag()->add('message', '¡¡HAS MODIFICADO TUS DATOS CORRECTAMENTE!!');

            //Una vez guardado redirigimos
            return $this->redirectToRoute('user_detail', [
                        'id' => $user->getId()
            ]);
        } //Fin if

        return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                    'edicion_usuario' => $edicion_usuario,
                    'user' => $user
        ]);
    }

    public function delete($id) {
        
        // Cargo repositorio
        $user_repo = $this->getDoctrine()->getRepository(User::class);

        //Busco el usuario que se quiere dar de baja por el id
        $user = $user_repo->find($id);
        
        $tasks=$user->getTasks();
        //Si no estas logado se redirecciona a la pagina principal
        if (is_null($user)) {
            return $this->redirectToRoute('inicio');
        }

        //Cargo entity manager para poder borrar
        $em = $this->getDoctrine()->getManager();

        //Compruebo si el usuario tiene tareas pendientes si las tiene doy aviso de que tiene 
        //que eliminar tareas,si no tiene tareas pendientes sigue adelante y borra
        if (count($user->getTasks()) == 0) {
            $em->remove($user);
            $em->flush();


            $session = new Session();
            $session->getFlashBag()->add('message', '¡¡TE HAS DADO DE BAJA CORRECTAMENTE!!');

            //return $this->render('user/mensajeusuarioborrado.html.twig');
            return $this->render('user/mensajeusuarioborrado.html.twig');
        } else { //SI USUARIO TIENE ALGUNA TAREA,NO PUEDE DARSE DE BAJA,SE MUESTRA MENSAJE FLASH 
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡NO TE PUEDES DAR DE BAJA,TIENES ALGUNA/S TAREA/S PENDIENTE/S O ACABADA/S GUARDADA/S!!');
            
            return $this->redirectToRoute('tasks',['tareasacabadas'=>'NO','ente'=>'particular']);
        }
        
        
    }
    
    
    //MUESTRA VISTA PARA INTRODUCIR EL EMAIL DEL USUARIO DEL CUAL QUEREMOS RESTABLECER PASSWORD
    public function restablecerpassword() {

        return $this->render('user/restablecerpassword.html.twig');
    }
    //METODO SOLO PARA MI COMO ADMINISTRADOR CUANDO ME LOGUE EN PANTALLA PRINCIPAL
    public function fraseseguridadtemporal(Request $request) {
        $email = $request->get('email');
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $user = $user_repo->findOneBy(['email' => $email]);
        if ($user == '') {
            $session = new Session();
            $session->getFlashBag()->add('message', 'Email NO EXISTE');
            return $this->redirectToRoute('inicio');
        } else {
            //Si existe el email,generamos frase seguridad con 8 numeros aleatorios
            $clavealeatoria = rand(0, 100000000);
            
            // Pruebas para mandar email
            //new GmailTransport('javierapariciogarrido@gmail.com','28febrero2011');
            /*
            $email=(new Email())
                    ->from('javierapariciogarrido@gmail.com')
                    ->to('javiapaga37@gmail.com')
                    ->subject('Restblecer password gestor de tareas')
                    ->text('Este email te lo mando de prueba porque tienes que elegir contraseña')
                    ->html('<p>Hola mundo</p>');
            //$transport = Transport::fromDsn('smtp://localhost');
            $this->mailer->send($email);
            
            echo "Hemos llegado hasta aqui es buena señal";
            die();
            */
            
            //Asignamos la clave de la frase provisional al usuario en cuestion
            $user->setFraseseguridad($clavealeatoria);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            //Sesion flash informando que la clave provisional se ha asignado
            $session = new Session();
            //Si implemento el envio de email desde aqui el contenido flash diria frase temporal
            //asignada se te ha mandado a tu email,vuelve a pantalla inicial de la aplicacion
            // para elegir la definitiva desde la aplicacion
            $session->getFlashBag()->add('message', "Clave {$user->getFraseseguridad()} asignada");
        }
        return $this->redirectToRoute('inicio');
    }
    
    // LLAMO A VISTA PARA RECOGER LA FRASE DE SEGURIDAD PROVISIONAL QUE LE HEMOS ASIGNADO Y LA QUE 
    //EL CLIENTE QUIERE JUNTO CON EMAIL,Y DESDE ESA VISTA SE LLAMA AL METODO DE ABAJO
    public function inicio_recuperarFrase(){
        return $this->render('user/recuperarfraseseguridad.html.twig');
    }
    
    
    public function recuperarfraseseguridaddefinitiva(Request $request,UserPasswordEncoderInterface $encoder){
        
        //Recojo datos del formulario y almaceno en variables 
        $email=$request->get('email');
        $fraseprovisional=$request->get('fraseprovisional');
        $frasedefinitiva=$request->get('frasedefinitiva');
        //Compruebo que ningun campo este vacio y si estan todos rellenos 
        if ($request->get('email')!=null && $request->get('fraseprovisional')!=null && $request->get('frasedefinitiva')!=null){
            //Valido el email que sea un email correcto
            $validator=Validation::createValidator();
            $errores=$validator->validate($email,[
                 new Email() 
            ]);
            //Si no es correcto mando sesion flash y muestro en vista 
            if(count($errores)!=0){
                $session=new Session();
                $session->getFlashBag()->add('error','EL FORMATO DEL EMAIL INTRODUCIDO NO ES CORRECTO');
                
                
            }else{ // Si es correcto el formato del email
                //Cargo repositorio de usuario y busco segun el email introducido
                $user_repo=$this->getDoctrine()->getRepository(User::class);
                $user=$user_repo->findOneBy(['email'=>$email]);
                
                // Si encuentro email 
                if($user !=null){
                    //Si la frase provisional introducida en formulario es igual a la que le hemos 
                    //enviado por email
                    if($fraseprovisional == $user->getFraseseguridad()){
                        //seteo la frase de seguridad encriptada que el usuario ha elegido
                        $user->setFraseseguridad($frasedefinitiva);
                        //Encripto la frase de seguridad que usuario eligio
                        $frasencoded = $encoder->encodePassword($user, $user->getFraseseguridad());
                        //seteo la frase de seguridad encriptada que el usuario ha elegido  y
                        $user->setFraseseguridad($frasencoded);
                        //guardo en bd
                        $em=$this->getDoctrine()->getManager();
                        $em->persist($user);
                        $em->flush();

                        //creo sesion flash indicando que se ha restablecido la frase correctamente
                        $session=new Session();
                        $session->getFlashBag()->add('message','¡¡SE HA RESTABLECIDO LA FRASE DE SEGURIDAD CORRECTAMENTE!!');
                        return $this->redirectToRoute('inicio');
                        
                    }else{
                        $session=new Session();
                        $session->getFlashBag()->add('error','¡¡NO HAS ESCRITO CORRECTAMENTE LA FRASE PROVISIONAL DE SEGURIDAD QUE TE HEMOS ENVIADO POR EMAIL !!');
                        
                    }
                    
                                  
                 //Si no encuentro el email en bd creo sesion flash con mensaje de que no coincide
                }else{
                    $session=new Session();
                    $session->getFlashBag()->add('error','¡¡EL EMAIL INTRODUCIDO NO SE ENCUENTRA REGISTRADO COMO USUARIO');
                                        
                }
                           
            }// FIN de la comprobacion de que el formato del email es correcto
            
            
        }else{  // SI HAY ALGUN CAMPO VACIO EN EL FORMULARIO
            $session=new Session();
            $session->getFlashBag()->add('error','¡¡FALTA ALGUN DATO POR RELLENAR!!');
            
        }
        
        
        
        
        return $this->render('user/recuperarfraseseguridad.html.twig');
    }
    
    
    // METODO QUE BUSCA EL EMAIL Y LUEGO PASA EL ID DEL USUARIO Y LA FRASE DE SEGURIDAD A VISTA
    //ELEGIRPASSWORD.HTML.TWIG
    public function localizaremail(Request $request) {
        $email = $request->get('email');
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $user = $user_repo->findOneBy(['email' => $email]);

        if (is_null($user)) {
            //Creo sesion flash con mensaje
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡NO PUEDES RECUPERAR LA CONTRASEÑA,NO EXISTE EL EMAIL INTRODUCIDO!!');

            //Redirigir a portada inicial
            return $this->redirectToRoute('inicio');
        } else { //SI ENCONTRAMOS EL CLIENTE,ALMACENAMOS SU ID Y FRASESEGURIDAD
            $id_usuario = $user->getId();

            $user_fraseseguridad = $user->getFraseseguridad();
            /*
              return $this->redirectToRoute('user_changepassword',[
              'id'=>$id_usuario,
              'fraseseguridad'=>$user_fraseseguridad
              ]);

             */
            return $this->render('user/elegirpassword.html.twig', [
                        'id' => $id_usuario,
                        'fraseseguridad' => $user_fraseseguridad
            ]);
        }
    }

    
    // METODO QUE CAMBIA LA CONTRASEÑA DEL CLIENTE SI FRASE DE SEGURIDAD ESTA OK
    public function comprobaremail(Request $request, UserPasswordEncoderInterface $encoder) {

        if ($request->get('password') == "") {
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡La contraseña no puede estar vacia!!');
            return $this->redirectToRoute('user_restablecerpassword');
        }

        // Almaceno El id del cliente que quiere cambiar la contraseña
        $id_cliente = $request->get('id_cliente');

        //Almaceno la frase seguridad real que el cliente tiene en bbdd
        $fraseseguridadreal = $request->get('fraseseguridadreal');

        //Almaceno la frase de seguridad que el cliente introduce por teclado 
        $fraseseguridad = $request->get('fraseseguridad');




        //Compruebo si la frase de seguridad que ha introducido el usuario es correcta
        //LA FUNCION password_verify se le pasa  el string sin codificar y la clave encriptada y
        //NOS DICE SI LO QUE INTRODUCIMOS SIN CODIFICAR COINCIDE CON LO QUE ESTA CODIFICADO,
        //ES DECIR INTERNAMENTE DECODIFICA CLAVE ENCRIPTADA Y COMPRUEBA SI COINCIDE
        if (!password_verify($fraseseguridad, $fraseseguridadreal)) {
            // SI LA FRASE DE SEGURIDAD NO ES CORRECTA
            //Creo sesion flash diciendo que la frase seguridad no coincide
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡NO SE HA PODIDO RESTABLECER LA CONTRASEÑA,LA FRASE DE SEGURIDAD QUE HAS INTRODUCIDO NO ES CORRECTA!!');

            //Redireccion a portada inicial
            return $this->redirectToRoute('inicio');
        }
        //SI LA FRASE DE SEGURIDAD ES CORRECTA SIGUE EL FLUJO DE PROGRAMA HACIA ABAJO CAMBIANDO LA
        //CONTRASEÑA
        $user = new User();

        $em = $this->getDoctrine()->getManager();
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $user = $user_repo->find($id_cliente);

        //Almaceno la nueva contraseña que el cliente quiere restablecer QUE LLEGA POR REQUEST(POST) 
        $new_password = $request->get('password');
        //Cifrar contraseña
        $encoded = $encoder->encodePassword($user, $new_password);
        // Seteo la contraseña cifrada
        $user->setPassword($encoded);

        $em->persist($user);
        $em->flush();

        //Creo sesion flash
        $session = new Session();
        $session->getFlashBag()->add('message', '¡¡CONTRASEÑA MODIFICADA CORRECTAMENTE,PUEDES INICIAR SESION CON TU NUEVA CONTRASEÑA!!');

        //Redireccion a portada inicial
        return $this->redirectToRoute('inicio');
    }

}
