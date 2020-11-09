'use strict';
$(document).ready(function(){   
   
    
    // HACE QUE EL TITULO GESTOR DE TAREAS APAREZCA ANIMANDOSE DE MAS PEQUEÑO A MAS GRANDE(JQUERY)
    $('#titulo').toggle('scale',3000);
    
    //$('#titulo').fadeIn('slow');
    //$('#titulo').fadeTo('slow',1);
    
    // Al darle a salir para cerrar sesion evento click para que pregunte si deseas salir
    $("#cerrarsesion").click(function(){
       var respuesta=confirm('¿Quieres cerrar sesion?');
       if(respuesta){
           $('body').load('http://proyecto-symfony.com.devel/logout');
           $('#title').html('GESTOR DE TAREAS');
       }
    });
    
    // CUANDO LE DAMOS AL BOTON BORRAR TAREA, NOS PREGUNTA SI ESTAMOS SEGUROS DE BORRARLA 
    $('.botonborrar').click(function(){
        
       var respuesta = confirm('¿Quieres realmente eliminar la tarea?');
       if(respuesta){
           var contenido = $(this).attr('name'); //En el atributo name le pasamos el id de la tarea
           $('body').load('http://proyecto-symfony.com.devel/borrar_tarea/'+contenido);
            // Busco si existe en el dom el menu de tareas de empresas
            var objetomenu =document.getElementById('todaslastareas');
            // Si no existe es que el cliente es particular,redirecciono a mis tareas
            if(objetomenu==null){
               //window.location.assign('http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular');
               //window.location.assign('http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular');
                //$('body').load('http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular');
                location.href="http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular";
                location.reload(true);
                location.reload(true);
                location.reload(true);
            }else{ // SI el cliente es de empresas redirecciono a tareas de empresas
               //window.location.assign('http://proyecto-symfony.com.devel/todas_las_tareas/NO/empresa');
               //window.location.assign('http://proyecto-symfony.com.devel/todas_las_tareas/NO/empresa');
               location.href="http://proyecto-symfony.com.devel/todas_las_tareas/NO/empresa";
               location.reload(true);
               location.reload(true);
            } 
            
       }
    }); 
    
    //DE JQUERY UI
    // Calendario para poder elegir la fecha prevista para finalizar la tarea
    
    $('.calendario').datepicker({dateFormat:'dd-mm-yy',minDate:0})
                    .keydown(function(event){
                        event.preventDefault();
                    });
    $('.calendario').on('copy',function(event){
                        event.preventDefault();        
                    })
                   .on('paste',function(event){
                        event.preventDefault();            
                    })
                    .attr('autocomplete','off'); 
    
    
    //Funcionalidad para que no se pueda meter datos en input de fecha prevista finalizar tarea
    $('#task_fechafin').keypress(function(event){
        event.preventDefault();
    });
    // Funcionalidad para que no se pueda pegar en ese input de fecha prevista
    $('#task_fechafin').on('paste',function(event){
        event.preventDefault();
    });
    
    // Funcionalidad para que no se pueda copiar en ese input de fecha prevista
    $('#task_fechafin').on('copy',function(event){
        event.preventDefault();
    });
    
    // QUITAMOS EL AUTOCOMPLETAR DE DIFERENTES INPUTS DE FORMULARIOS QUE RESULTAN INSEGUROS
    $('#task_fechafin').attr('autocomplete','off');
    $('#email').attr('autocomplete','off');
    $('#fraseseguridad').attr('autocomplete','off');
    $('#username').attr('autocomplete','off');
    
    
    // FUNCIONALIDADES DE AYUDA (CARGAMOS LOS METODOS DE AYUDA POR LOAD DESDE JAVASCRIPT)
      
    
    // Al darle click a ayuda carga el controlador de la ruta ayuda
    // y utilizamos localstorage para indicar que accedemos a la ayuda a traves de menu principal
    //de ayuda.
    $('#ayuda').click(function(){
        localStorage.setItem('ayuda','menu de ayuda');
        //CArgamos controlador de ayuda para ver la ayuda
        $('body').load('http://proyecto-symfony.com.devel/ayuda');
       // Pongo la url del navegador manualmente ya que sola no se cambia
       window.location.href='http://proyecto-symfony.com.devel/ayuda';
       
    });
    
    //Al pasar el raton encima de menu ayuda si no estamos en ayuda que las letras se vean negras 
    $('#ayuda').mouseover(function(){
        if(window.location.href!= 'http://proyecto-symfony.com.devel/ayuda'){
            $(this).css('color','black');
        }
        
    });
    
    //Al quitar el raton de la opcion ayuda del menu las letras se vuelven doradas 
    $('#ayuda').mouseleave(function(){
        if(window.location.href!="http://proyecto-symfony.com.devel/ayuda"){
            $(this).css('color','#C89C70');
        }
    });
    
      
    
    
    // PONER RELOJ A LA DERECHA ARRIBA 
    function timer(){
        //ESTO ES EN JAVASCRIPT PURO 
        //var fecha = new Date().toLocaleString();
        
        // Fecha con libreria moment js
        var fecha = moment().format('D MMMM  YYYY,HH:mm:ss ');
        $('#hora').animate({marginRight:'-12%'},1500);
        $('#hora').css('color','black')
                         .css('text-shadow','4px 3px 2px #C89C70,3px 2px 1px #C89C70,2px 1px 0px #C89C70,1px 0px 0px #C89C70');
        
        $('#hora').text('HOY ES '+fecha);
        
    }
    
    var tiempo =setInterval(timer,1000);
    
    
    
    // Saco leyenda al pasar el raton por encima con breve explicacion de cada opcion del menu
    // con jquery ui
    $(document).tooltip();    
    
    //ALERTA QUE AVISA QUE VAS A BORRAR A USUARIO Y PREGUNTA SI ESTAS SEGURO
    
    $('#dardebaja').click(function(event){
        event.preventDefault();
        var respuesta=confirm('HAS ELEGIDO LA OPCIÓN DE DARTE DE BAJA,¿ESTAS SEGURO?');
        
        if(respuesta){
            
            // CARGO EL METODO DE DAR DE BAJA EL USUARIO 
            $('body').load('http://proyecto-symfony.com.devel/dardebaja/'+$(this).attr('name'));
            
            
            //$('body').load('http://proyecto-symfony.com.devel/');
            //window.location.assign('http://proyecto-symfony.com.devel/');
            //window.location.assign('http://proyecto-symfony.com.devel/');
            //var objetomenu =document.getElementById('todaslastareas');
            //if(objetomenu==null){
             //   $('body').load('http://proyecto-symfony.com.devel/');
            //}else{
                //location.href='http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular';
                //$('body').load('http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular');
                //window.location.assign('http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular');
            //}
            
            //location.reload(true);
            //window.location.replace('http://proyecto-symfony.com.devel/'); // Cargo la url inicial del proyecto.
            //location.reload(true);
        }
    });
    
    
   // Enlace del menu superior de la vista que aparece cuando nos damos de baja como usuario
   //  de volver a pantalla inicial una vez dado de baja de la aplicacion,si damos click. 
   $('#iniciarsesion').click(function(){
        $('body').load('http://proyecto-symfony.com.devel/logout');// Cerramos sesion para que no pete
        $('body').load('http://proyecto-symfony.com.devel/');//Volvemos a pagina principal proyecto
        window.location.assign('http://proyecto-symfony.com.devel/');//Asignamos url principal
   });
    
   
   
    //Ocultar el valor del input de la frase de seguridad que aparecia relleno,lo borramos
    $('#changepassword_fraseseguridad').val(' ');
    
    
    //EFECTO DE Scroll hacia arriba AL PULSAR FLECHA DE ABAJO A LA DERECHA PARA QUE SUBA LA PAGINA  
    
    $('.subir').click(function(){
       $('html,body').animate({
           scrollTop:0
       },1000);
       
    });
    // Cambiar el color de la imagen de subir por otra imagen que he retocado
    // y ademas se sube hacia arriba en una pequeña animacion
    $('.subir img').mouseover(function(){
        $(this).attr('src','http://proyecto-symfony.com.devel/assets/img/flecha_arriba2.jpg')
               .animate({'marginTop':'-50%'},1000);
    });
    
    $('.subir img').mouseleave(function(){
        $(this).attr('src','http://proyecto-symfony.com.devel/assets/img/flecha_arriba.jpg')
               .animate({'marginTop':'-28%'},1000);
    });
    
    
    //SEÑALAR EN EL MENU PRINCIPAL EL APARTADO EN EL QUE ESTAMOS,MARCANDO EN EL MENU CON SEÑAL
    //Recojo en variable la url que está en estos momentos para pintar opcion que estemos en rojo
    
    var urlactual = window.location.href; 
    
    
    //Creo array con ids de elementos de menu
    var idsmenu = ['todaslastareas','mistareas','crear_tarea','misdatos','iniciarsesion','registro','ayuda'];
    
    //REcorro el array de elementos que estan cargados en pagina y si coincide con url activa 
    //subrayamos el elemento del menu(FUNCIONALIDAD PARA TODAS OPCIONES EXCEPTO SALIR  Y AYUDA  
    /*
    idsmenu.forEach(function(elemento){
        if(typeof $('#'+elemento).attr('href') !=='undefined'){
            var url='http://proyecto-symfony.com.devel'+ $('#'+elemento).attr('href');
            
            if(url == urlactual){
                $('#'+elemento).css('text-decoration','underline')
                               .css('color','red');
            }
        }
    }); // FIN FOREACH
    */
    // Para que señale de rojo y subraye la opcion del menu cuando tenga dentro mas opciones
    
    if(urlactual=='http://proyecto-symfony.com.devel/todas_las_tareas/NO/empresa' || urlactual=='http://proyecto-symfony.com.devel/todas_las_tareas/SI/empresa' || urlactual.startsWith('http://proyecto-symfony.com.devel/avance_tareas/empresa') || urlactual.startsWith('http://proyecto-symfony.com.devel/retroceso_tareas/empresa') || urlactual=='http://proyecto-symfony.com.devel/buscar_tarea_por_titulo/empresa'){
        $('#todaslastareas').css('text-decoration','underline')
                      .css('color','red');
    }  
    
    if (urlactual=='http://proyecto-symfony.com.devel/todas_las_tareas/NO/particular' || urlactual=='http://proyecto-symfony.com.devel/todas_las_tareas/SI/particular' || urlactual.startsWith('http://proyecto-symfony.com.devel/avance_tareas/particular')|| urlactual.startsWith('http://proyecto-symfony.com.devel/retroceso_tareas/particular') || urlactual=='http://proyecto-symfony.com.devel/buscar_tarea_por_titulo/particular' ) {
        $('#mistareas').css('text-decoration','underline')
                               .css('color','red');
    }
    
    if(urlactual=='http://proyecto-symfony.com.devel/crear_tarea'){
        $('#crear_tarea').css('text-decoration','underline')
                         .css('color','red');
    }
    
    if(urlactual.startsWith('http://proyecto-symfony.com.devel/modificar_usuario') || urlactual.startsWith('http://proyecto-symfony.com.devel/detalle_usuario')|| urlactual.startsWith('http://proyecto-symfony.com.devel/elige_contra')){
        $('#misdatos').css('text-decoration','underline')
                      .css('color','red');
    }
        
    // Si pulsamos opcion de soporte tecnico en pie de pagina llevamos a seccion ayuda y 
    //en localstorage almacenamos que vamos a la ayuda a traves de la opcion soporte tecnico
    //para que directamente al entrar en acordeon entremos en opcion soporte tecnico
    $('#soportetecnico').click(function(event){
        event.preventDefault();
        localStorage.setItem('ayuda','pie de pagina');
        $('body').load('http://proyecto-symfony.com.devel/ayuda');
        window.location.href='http://proyecto-symfony.com.devel/ayuda';
    });
    
    // En pantalla login abajo hay enlace por si olvidas frase de seguridad,la funcionalidad
    //que esta abajo te permite al darle click a soporte tecnico te lleva a la pantalla de
    //soporte tecnico dentro de ayuda
    $('#restablecerfrase').click(function(event){
        event.preventDefault();
        localStorage.setItem('ayuda','pie de pagina');
        $('body').load('http://proyecto-symfony.com.devel/ayuda');
        window.location.href='http://proyecto-symfony.com.devel/ayuda';
    });
    
     
    
    //Al dar de alta a un usuario o editar un usuario,quito atributo required a nivel  html
    //  en el input donde hay que poner el nombre de la empresa a la que pertenece el usuario
    // ya que ese campo no es obligatorio
    $('#register_enterprise').removeAttr('required');
    $('#edituser_enterprise').removeAttr('required');
    
    
    //Modificar en el registro el input de frase de seguridad para que sea tipo password y no se
    //vea lo que se escribe como la contraseña
    $('#register_fraseseguridad').attr('type','password');
    
    //ANIMACION CON JQUERY DE LA FRASE INICIAL EN LA PORTADA DE LA WEB JUSTO ENCIMA
    // DE LA IMAGEN DE LA PORTADA CUANDO NO HAY NADIE LOGADO
    $('#Bienvenida1').animate({opacity:'1'},3000);
    
        
});
    
      



    