<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request; //AÃ±ado  peticion del formulario por post 
use App\Form\TaskType;
use App\Form\TaskType_creation;
use Symfony\Component\Security\Core\User\UserInterface; // Importo el logado en sesion
use Symfony\Component\HttpFoundation\Session\Session;

//use Doctrine\ORM\Tools\Pagination\Paginator;



class TaskController extends AbstractController {

    public function portadaapp() {

        return $this->render('task/portadainicial.html.twig');
    }

    // METODO DE TODAS LAS TAREAS PENDIENTES O ACABADAS DE LA EMPRESA PAGINADAS
    public function index(UserInterface $user, $tareasacabadas, $ente) {
        
          
         
        // Array usado para almacenar todas las tareas de de la misma empresa  
        //Esta variable nos ayuda a hacer el limit,empezamos por 0. 
        $pagina = 0;
        $paginaenlaqueestamos = 1;
        //Almaceno la empresa del usuario logado
        $enterprise = $user->getEnterprise();



        if ($ente == "empresa") {
            $tareastodosempleados = [];
            $indice = 0;


            // Hago consulta de todas las tareas NO ACABADAS de la base de datos ordenadas de mas actual 
            //a menos
            $task_repo = $this->getDoctrine()->getRepository(Task::class);
            $tasks = $task_repo->findBy(['acabada' => $tareasacabadas], ['id' => 'DESC']);



            foreach ($tasks as $task) {
                //Compruebo de cada tarea la empresa del usuario que la creo,si es igual a la 
                //empresa de la persona que esta logada,almacena esa tarea en el array
                //tareastodosempleados
                if ($task->getUser()->getEnterprise() == $enterprise && $enterprise != '') {
                    $tareastodosempleados[$indice] = $task;
                    $indice++;
                }
            }
            $alltasks = $tareastodosempleados;

            //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS 
            //MEDIANTE SQL
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id IN(SELECT id FROM users WHERE enterprise='{$enterprise}') AND acabada='{$tareasacabadas}' ORDER BY id DESC LIMIT {$pagina},10; ";

            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll();
        } //FIN DE PROCESOS PROPIOS DE  SI LAS TAREAS SON DE EMPRESA


        if ($ente == 'particular') {
            $task_repo = $this->getDoctrine()->getRepository(Task::class);
            //todas LAS TAREAS DEL USUARIO ACABADAS O SIN ACABAR DEPENDE DEL BOTON QUE CONSULTE DEL MENU
            $alltasks = $task_repo->findBy(['user' => $user->getId(), 'acabada' => $tareasacabadas], ['id' => 'DESC']);

            $iduser = $user->getId();
            //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS MEDIANTE SQL
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id={$iduser} AND acabada='{$tareasacabadas}' ORDER BY id DESC LIMIT {$pagina},10;";

            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll();
        }  // FIN PROCEDIMIENTOS CUANDO SON TAREAS PARTICULARES
        //PAGINACION
        //Para tema paginacion empezamos por la pagina 1 que es la que almaceno en variable
        //PARA SABER EL NUMERO DE PAGINAS QUE TENEMOS 
        $longitudtareas = count($alltasks); // Contamos primero todas las tareas
        //almacenamos el tipo de dato del resultado de dividir las tareas entre n de tareas que queremos x pagina
        $tipodedato = gettype($longitudtareas / 10);

        //Si la division da entero, las paginas seran el resultado directamente
        if ($tipodedato === 'integer') {
            $totalpaginas = $longitudtareas / 10;
        }

        // Si el tipo de dato de la division es decimal se redondea al siguiente entero        
        if ($tipodedato === 'double') {
            $totalpaginas = ceil($longitudtareas / 10);
        }

        // COMPRUEBO SI HAY ALGUNA TAREA PASADA DE FECHA Y SI ES ASI PASO MENSAJE FLASH A VISTA
        // ESTO ES SOLO PARA CUANDO ESTAMOS EN APARTADO TAREAS DE PARTICULAR
        $tareasfueradeplazo = 0;
        $fechasrojas = []; // Array donde almaceno el valor de las fechas de las tareas cuya fecha
        // estimada para realizarse ya ha pasado,(tarea fuera de plazo)
        //para despues en la vista de mis tareas pintar de rojo la tarea que este
        //fuera de plazo
        $indicefechasrojas = 0;
        foreach ($alltasks as $task) {
            $fechatarea = new \DateTime($task->getFechafin()); //Recojo fecha fin de tarea
            $fechadehoy = new \DateTime("now"); //Recojo fecha del dia de hoy

            if ($fechatarea < $fechadehoy) {
                $tareasfueradeplazo++; //Sumo el contador para luego decir las tareas fuera d plazo
                $fechasrojas[$indicefechasrojas] = $task->getFechafin();
                $indicefechasrojas++;
            }
        }
        if ($tareasacabadas == 'NO') {
            // CREO EL MENSAJE FLASH SI HAY ALGUNA TAREA CON EL PLAZO DE FINALIZACION VENCIDO              
            if ($ente == 'particular') {
                if ($tareasfueradeplazo > 0) {
                    $session = new Session();
                    $session->getFlashBag()->add('tareaplazovencido', "¡¡TIENES {$tareasfueradeplazo} TAREA/S FUERA DE PLAZO,REVÍSALAS!!");
                }
            }
        } // FIN DE SI LAS TAREAS NO ESTAN ACABADAS PARA COMPROBAR TAREAS PASADAS DE FECHA





        return $this->render("task/index.html.twig", [
                    'alltasks' => $alltasks, // Todas las tareas sin paginar
                    'tasks' => $tasks, //Tareas paginadas
                    'enterprise' => $enterprise, // Pasamos a vista el nombre de la empresa 
                    'pagina' => $pagina, //numero de pagina inicio para hacer paginacion desde sql.
                    'paginaenlaqueestamos' => $paginaenlaqueestamos,
                    'totalpaginas' => $totalpaginas, // Pasamos el numero de paginas que hay en total
                    'fechasrojas' => $fechasrojas, // Array con fechas para pintar de rojo en vista
                    'tareasacabadas' => $tareasacabadas, //Para saber si hay que listar tareas acabadas
                    'ente' => $ente  //Pasamos a vista si se trata de listar tareas empresa o particular
        ]);
    }
    
    
    

    // METODO DE AVANCE DE PAGINA DE LAS TAREAS DE LA EMPRESA PENDIENTES   
    public function Paginacionindex(UserInterface $user, $ente, $paginas, $paginaenlaqueestamos, $acabado, $sentido) {
        // LO PRIMERO TANTO SI LAS TAREAS SON DE EMPRESA COMO DE PARTICULAR,HAY QUE SUMAR DIEZ 
        // O RESTAR 10 EN FUNCION DE PARAMETRO sentido PARA PODER HACER EL LIMIT EN SQL
        //  Y SUMAR UNA PAGINA MAS 
        if ($sentido == 'avanzar') {
            $paginas = $paginas + 10; //Sumamos 10 la la pagina porque queremos 10 tareas por pagina
            //Sumamos uno a la pagina en la que estamos:
            $paginaenlaqueestamos++;
        }
        if ($sentido == 'retroceder') {
            $paginas = $paginas - 10; //Restamos 10 la la pagina porque queremos 10 tareas por pagina
            //Restamos uno a la pagina en la que estamos:
            $paginaenlaqueestamos--;
        }


        if ($ente == 'empresa') {
            // Array usado para almacenar todas las tareas de de la misma empresa  
            $tareastodosempleados = [];
            $indice = 0;
            //Almaceno la empresa del usuario logado para pasar al metodo de repositorio
            $enterprise = $user->getEnterprise();


            // Hago consulta de todas las tareas de la base de datos ordenadas de mas actual a menos
            $task_repo = $this->getDoctrine()->getRepository(Task::class);
            $tasks = $task_repo->findBy(['acabada' => $acabado], ['id' => 'DESC']);


            foreach ($tasks as $task) {
                //Compruebo de cada tarea la empresa del usuario que la creo,si es igual a la 
                //empresa de la persona que esta logada,almacena esa tarea en el array
                //tareastodosempleados
                if ($task->getUser()->getEnterprise() == $enterprise && $enterprise != '') {
                    $tareastodosempleados[$indice] = $task;
                    $indice++;
                }
            }
            $alltasks = $tareastodosempleados;

            //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS MEDIANTE SQL
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id IN(SELECT id FROM users WHERE enterprise='{$enterprise}') AND acabada='{$acabado}' ORDER BY id DESC LIMIT {$paginas},10; ";

            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll();
        }

        if ($ente == 'particular') {
            $task_repo = $this->getDoctrine()->getRepository(Task::class);
            $alltasks = $task_repo->findBy(['user' => $user->getId(), 'acabada' => $acabado], ['id' => 'DESC']);

            $iduser = $user->getId(); // Almacenamos el id del usuario que esta logado
            //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS MEDIANTE SQL

            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id={$iduser} AND acabada='{$acabado}' ORDER BY id DESC LIMIT {$paginas},10;";

            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll();
        }

        // PARTE COMUN TANTO SI ESTAMOS HABLANDO DE TAREAS PROPIAS O DE EMPRESAS 
        //PARA SABER EL NUMERO DE PAGINAS QUE TENEMOS 
        $longitudtareas = count($alltasks); // Contamos primero todas las tareas
        //almacenamos el tipo de dato del resultado de dividir las tareas entre n de tareas que queremos x pagina
        $tipodedato = gettype($longitudtareas / 10);

        //Si la division da entero, las paginas seran el resultado directamente
        if ($tipodedato === 'integer') {
            $totalpaginas = $longitudtareas / 10;
        }

        // Si el tipo de dato de la division es decimal se redondea al siguiente entero        
        if ($tipodedato === 'double') {
            $totalpaginas = ceil($longitudtareas / 10);
        }


        if (count($tasks) == 0) {
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡NO HAY MAS TAREAS!!');
        }


        // COMPRUEBO SI HAY ALGUNA TAREA PASADA DE FECHA Y SI ES ASI PASO MENSAJE FLASH A VISTA

        $tareasfueradeplazo = 0;
        $fechasrojas = []; // Array donde almaceno el valor de las fechas de las tareas cuya fecha
        // estimada para realizarse ya ha pasado,(tarea fuera de plazo)
        //para despues en la vista de mis tareas pintar de rojo la tarea que este
        //fuera de plazo
        $indicefechasrojas = 0;
        foreach ($alltasks as $task) {
            $fechatarea = new \DateTime($task->getFechafin()); //Recojo fecha fin de tarea
            $fechadehoy = new \DateTime("now"); //Recojo fecha del dia de hoy

            if ($fechatarea < $fechadehoy) {
                $tareasfueradeplazo++; //Sumo el contador para luego decir las tareas fuera d plazo
                $fechasrojas[$indicefechasrojas] = $task->getFechafin();
                $indicefechasrojas++;
            }
        }

        if ($sentido == 'retroceder' && $ente == 'particular' && $acabado == 'NO') {
            // CREO EL MENSAJE FLASH SI HAY ALGUNA TAREA CON EL PLAZO DE FINALIZACION VENCIDO              
            if ($tareasfueradeplazo > 0 && $acabado == 'NO' && $ente == 'particular') {
                $session = new Session();
                $session->getFlashBag()->add('tareaplazovencido', "¡¡TIENES {$tareasfueradeplazo} TAREA/S FUERA DE PLAZO,REVÍSALAS!!");
            }
        }


        return $this->render("task/index.html.twig", [
                    'tasks' => $tasks, // Tareas paginadas
                    'alltasks' => $alltasks, //Todas las tareas sin paginar 
                    'pagina' => $paginas, //valor para el limit
                    'paginaenlaqueestamos' => $paginaenlaqueestamos, //Para visualizar en vista pagina q estamos
                    'totalpaginas' => $totalpaginas,
                    //'enterprise' => $enterprise,
                    'tareasacabadas' => $acabado,
                    'fechasrojas' => $fechasrojas,
                    'ente' => $ente
        ]);
    }

    // RECOJE EL TITULO Y SI PERTENECE A EMPRESA O NO HACE BUSQUEDA Y ENVIA RESULTADO A VISTA
    public function searchByTitle(Request $request, UserInterface $user) {

        //Proceso la informacion que nos llega por request del formulario de busqueda
        // Si la busqueda es de una tarea de la empresa 
        //Esta variable nos ayuda a hacer el limit,empezamos por 0. 
        $pagina = 0;

        //SI LA BUSQUEDA ES DE UNA TAREA DE EMPRESA 
        if ($request->get('entidad') === 'empresa') {
            //Almaceno la empresa del usuario logado para consultar las tareas de la empresa del usuario
            $empresausuario = $user->getEnterprise();
            //SI EL TITULO ESTA VACIO CREO SESION CON ERROR
            if ($request->get('title') === '') {
                $session = new Session();
                $session->getFlashBag()->add('error', '¡¡Tienes que introducir el titulo a buscar!!');
                return $this->redirectToRoute('tasks_viewsearchbytitle', ['ente' => 'empresa']);
            }
            $titulo = $request->get('title');

            // Hago conexion para hacer consulta sql 
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id IN(SELECT id FROM users WHERE enterprise='{$empresausuario}')AND title LIKE '%{$titulo}%' ";

            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $alltasks = $prepare->fetchAll();
            //TODAS LAS TAREAS CON EL TITULO BUSCADO SIN PAGINAR
            //AHORA CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE 
            //DATOS MEDIANTE SQL
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id IN(SELECT id FROM users WHERE enterprise='{$empresausuario}')AND title LIKE '%{$titulo}%' LIMIT {$pagina},10 ";
            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll(); // TAREAS PAGINADAS DE LA EMPRESA QUE COINCIDAN CON TITULO
        }  // FIN DE SI LA BUSQUEDA ES DE EMPRESA  
        //// Si la tarea a buscar es de usuario particular
        if ($request->get('entidad') === 'particular') {
            //SI EL TITULO ESTA VACIO CREO SESION CON ERROR
            if ($request->get('title') === '') {
                $session = new Session();
                $session->getFlashBag()->add('error', '¡¡Tienes que introducir el titulo a buscar!!');
                return $this->redirectToRoute('tasks_viewsearchbytitle', ['ente' => 'particular']);
            }
            //Recojo id usuario 
            $idusuario = $user->getId();
            // Recojo titulo
            $titulo = $request->get('title');

            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id={$idusuario} AND title LIKE '%{$titulo}%' ";
            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $alltasks = $prepare->fetchAll(); // TODAS TAREAS PARTICULARES CUYO TITULO COINCIDE 
            //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS MEDIANTE SQL
            $connection = $this->getDoctrine()->getConnection();
            $sql = "SELECT * FROM tasks WHERE user_id={$idusuario} AND title LIKE '%{$titulo}%'  LIMIT {$pagina},10 ";
            $prepare = $connection->prepare($sql);
            $prepare->execute();
            $tasks = $prepare->fetchAll();
            //TAREAS DE USUARIO SEGUN TITULO BUSCADO PAGINADAS DE 10 EN 10 
        }


        //PAGINACION
        //Para tema paginacion empezamos por la pagina 1 que es la que almaceno en variable
        $paginaenlaqueestamos = 1;

        //PARA SABER EL NUMERO DE PAGINAS QUE TENEMOS 
        $longitudtareas = count($alltasks); // Contamos primero todas las tareas encontradas
        //almacenamos el tipo de dato del resultado de dividir las tareas entre n de tareas que queremos x pagina
        $tipodedato = gettype($longitudtareas / 10);

        //Si la division da entero, las paginas seran el resultado directamente
        if ($tipodedato === 'integer') {
            $totalpaginas = $longitudtareas / 10;
        }

        // Si el tipo de dato de la division es decimal se redondea al siguiente entero        
        if ($tipodedato === 'double') {
            $totalpaginas = ceil($longitudtareas / 10);
        }



        return $this->render('task/resultadosbusqueda.html.twig', [
                    'tasks' => $tasks, // TAREAS PAGINADAS
                    //'alltasks' => $alltasks,//TAREAS SIN PAGINAR
                    'totalpaginas' => $totalpaginas, //NUMERO TOTAL DE TODAS LAS PAGINAS
                    'paginaenlaqueestamos' => $paginaenlaqueestamos, //PAGINA ACTUAL 
                    'pagina' => $pagina, // Indicamos desde donde partimos para hacer el limit
                    'ente' => $request->get('entidad'), //Para saber si buscamos en empresa o particula
                    'titulo' => $titulo, //Titulo de la tarea que estamos buscando
                    'longitudtareas' => $longitudtareas // Numero de tareas encontradas en busqueda 
        ]);
    }

    //METODO AVANCE DE TAREAS BUSCADAS POR TITULO
    public function PaginacionBusqueda(UserInterface $user, $paginas, $paginaenlaqueestamos, $longitudtareas, $titulo, $ente, $sentido) {


        //Almaceno la empresa del usuario logado para pasar al metodo de repositorio
        $enterprise = $user->getEnterprise();

        //Almaceno el id de usuario para hacer consulta de las tareas individuales 
        $idusuario = $user->getId();


        //PARA SABER EL NUMERO DE PAGINAS QUE TENEMOS 
        //almacenamos el tipo de dato del resultado de dividir las tareas entre n de tareas que queremos x pagina
        $tipodedato = gettype($longitudtareas / 10);

        //Si la division da entero, las paginas seran el resultado directamente
        if ($tipodedato === 'integer') {
            $totalpaginas = $longitudtareas / 10;
        }

        // Si el tipo de dato de la division es decimal se redondea al siguiente entero        
        if ($tipodedato === 'double') {
            $totalpaginas = ceil($longitudtareas / 10);
        }
        if ($sentido == 'avanzar') {
            $paginas = $paginas + 10; //Sumamos 10 la la pagina porque queremos 10 tareas por pagina
            //Sumamos uno a la pagina en la que estamos:
            $paginaenlaqueestamos++;
        }

        if ($sentido == 'retroceder') {
            $paginas = $paginas - 10; //Restamos 10 la la pagina porque queremos 10 tareas por pagina
            //Sumamos uno a la pagina en la que estamos:
            $paginaenlaqueestamos--;
        }



        //CON LIMIT HAGO CONSULTA PARA SACAR LAS TAREAS DE 10 EN 10 DESDE LA BASE DE DATOS MEDIANTE SQL
        $connection = $this->getDoctrine()->getConnection();
        if ($ente === 'empresa') {
            $sql = "SELECT * FROM tasks WHERE user_id IN(SELECT id FROM users WHERE enterprise='{$enterprise}')AND title LIKE '%{$titulo}%' LIMIT {$paginas},10; ";
        } else {
            $sql = "SELECT * FROM tasks WHERE user_id={$idusuario} AND title LIKE '%{$titulo}%'  LIMIT {$paginas},10; ";
        }


        $prepare = $connection->prepare($sql);
        $prepare->execute();
        $tasks = $prepare->fetchAll();
        if (count($tasks) == 0) {
            $session = new Session();
            $session->getFlashBag()->add('error', '¡¡NO HAY MAS TAREAS!!');
        }


        return $this->render('task/resultadosbusqueda.html.twig', [
                    'tasks' => $tasks, // Tareas paginadas
                    'ente' => $ente,
                    'pagina' => $paginas, //valor para el limit
                    'paginaenlaqueestamos' => $paginaenlaqueestamos, //Para visualizar en vista pagina q estamos
                    'totalpaginas' => $totalpaginas,
                    'titulo' => $titulo,
                    'longitudtareas' => $longitudtareas,
                        //'alltasks'=>$longitudtareas
        ]);
    }
    
    //METODO QUE RECOJE EL CAMPO A BUSCAR Y SI BUSCAMOS EN EMPRESA O PARTICULAR
    //Y PASAMOS TODOS LOS DATOS A PROCEDIMIENTO searchByTitle PARA QUE PROCESE 
    public function viewTaskByTitle($ente) {

        return $this->render('task/searchtaskbytitle.html.twig', [
                    'ente' => $ente
        ]);
    }
    
    // DETALLE DE UNA TAREA

    public function detail($id) {
        $task_repo = $this->getDoctrine()->getRepository(Task::class);
        $task = $task_repo->find($id);

        if (!$task) {
            return $this->redirectToRoute('inicio');
        }

        // CALCULAR DIFERENCIAS DE FECHAS ENTRE EL DIA DE HOY Y EL DIA PREVISTO PARA ACABAR LA
        // TAREA

        $fechatarea = new \DateTime($task->getFechafin());
        $fechadehoy = new \DateTime("now");
        $diferencia = $fechatarea->diff($fechadehoy);
        
        
        
        
        $dias = $diferencia->d;
        $meses = $diferencia->m;
        $ano = $diferencia->y;
        $horas = $diferencia->h;

        
        
        return $this->render('task/detail.html.twig', [
                    'task' => $task,
                    'dias' => $dias,
                    'meses' => $meses,
                    'ano' => $ano,
                    'horas' => $horas,
                    'fechadehoy'=>$fechadehoy,
                    'fechatarea'=>$fechatarea
        ]);
    }

    // CREAR UNA TAREA
    public function creation(Request $request, UserInterface $user) {
        //Instanciamos objeto Task
        $task = new Task();

        //Creamos formulario
        $form = $this->createForm(TaskType_creation::class, $task);

        //Seteo lo que me llega por post para rellenar el objeto task
        $form->handleRequest($request);

        //Comprobamos si el formulario se envia y es valido
        if ($form->isSubmitted() && $form->isValid()) {
            //Seteamos la fecha actual de la tarea creada
            $task->setCreatedAt(new \Datetime('now'));

            //Seteamos el campo acabada a NO
            $task->setAcabada('NO');
            //Obtengo el usuario que ha creado la tarea de la libreria userinterface
            $task->setUser($user);

            //Cargo entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            //Creo sesion flash para mostrar que la tarea se ha creado correctamente
            $session = new Session();
            $session->getFlashBag()->add('message', '¡¡TAREA CREADA CORRECTAMENTE!!');

            //Redirecciono a indice general de tareas
            return $this->redirectToRoute('tasks', [
                        'tareasacabadas' => 'NO',
                        'ente' => 'particular'
            ]);
        }

        //Cargo vista en la que va a estar el formulario para crear la tarea
        return $this->render('task/creation.html.twig', [
                    'form' => $form->createView()
        ]);
    }

// FIN METODO CREATION
    //METODO PARA EDITAR UNA TAREA
    public function edit(UserInterface $user, Request $request, $id) {
        // Cargo Repositorio y busco la tarea con el id que le pasamos 
        $task_repo = $this->getDoctrine()->getRepository(Task::class);
        $task = $task_repo->find($id);

        //Si no estamos logados o estamos logados pero no hemos creado la tarea,
        //O si el id de la tarea a modificar no existe nos redirecciona

        if (!$task)
            return $this->redirectToRoute('inicio');
        if (!$user || $user->getId() != $task->getUser()->getId()) {
            return $this->redirectToRoute('inicio');
        }

        //Creamos formulario
        $form = $this->createForm(TaskType::class, $task);

        //Seteo lo que me llega por post para rellenar el objeto task
        $form->handleRequest($request);

        //Comprobamos si el formulario se envia y es valido

        if ($form->isSubmitted() && $form->isValid()) {
            // PARA SABER SI LA TAREA QUE BORRAMOS EN DE PARTICULAR O DE EMPRESA PARA ASI REDIRIGIR 
            // CUANDO ESTE EDITADA A TODAS LAS TAREAS O PARTICULAR O DE EMPRESA 
            if (is_null($task->getUser()->getEnterprise())) {
                $redirigirtareas = 'particular';
            } else {
                $redirigirtareas = 'empresa';
            }


            //Cargo entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            // Creo sesion flash indicando que la tarea se ha modificado correctamente
            $session = new Session();
            $session->getFlashBag()->add('message', '¡¡TAREA MODIFICADA CORRECTAMENTE!!');

            return $this->redirectToRoute('tasks', [
                        'tareasacabadas' => 'NO',
                        'ente' => $redirigirtareas
            ]);
        } // FIN $form->isSubmitted() && $form->isValid() 

        return $this->render('task/creation.html.twig', [
                    'form' => $form->createView(),
                    'edit' => true
        ]);
    }

    // METODO PARA BORRAR UNA TAREA
    public function delete($id, UserInterface $user) {
        
        //Cargo Repositorio
        $task_repo = $this->getDoctrine()->getRepository(Task::class);
        //Catgo entity manager
        $em = $this->getDoctrine()->getManager();
        //Busco tarea a borrar
        $task = $task_repo->find($id);

        //Si no existe el id de la tarea a borrar o no estoy logado o no soy el dueño de la tarea
        //se redirecciona
        if (!$task)
            return $this->redirectToRoute('inicio');
        if (!$user || $user->getId() != $task->getUser()->getId()) {
            return $this->redirectToRoute('inicio');
        }

        // PARA SABER SI LA TAREA QUE BORRAMOS EN DE PARTICULAR O DE EMPRESA PARA ASI REDIRIGIR 
        // CUANDO ESTE EDITADA A TODAS LAS TAREAS O PARTICULAR O DE EMPRESA 
        if (is_null($task->getUser()->getEnterprise())) {
            $redirigirtareas = 'particular';
        } else {
            $redirigirtareas = 'empresa';
        }




        //Elimino la tarea de memoria doctrine 
        $em->remove($task);

        //Elimino de la base de datos el objeto de la tarea
        $em->flush();

        //Creo sesion flash para mostrar que la tarea se ha borrado
        $session = new Session();
        $session->getFlashBag()->add('message', '¡¡TAREA BORRADA CORRECTAMENTE!!');
        
        return $this->redirectToRoute('tasks',['tareasacabadas'=>'NO','ente'=>$redirigirtareas]);
        
    }

}
