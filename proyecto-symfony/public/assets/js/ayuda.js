$(document).ready(function(){
    //Aqui controlamos cuando venimos de fuera de ayuda desde la pagina principal en donde hemos 
 //utilizado localstorage para saber si venimos desde la opcion de soporte o la opcion de 
 //ayuda del menu principal
 if(localStorage.getItem('ayuda')==='menu de ayuda'){
    
     $('#acordeon').accordion({'active':0});
 }   
 if(localStorage.getItem('ayuda')==='pie de pagina'){
     
     $('#acordeon').accordion({'active':2});
 }
    
    
    
    //Ya dentro de la página de ayuda si le damos a opcion soporte tecnico O contacto
    // de pie de pagina directamente cargamos en acordeon la opcion de soporte.
    $('#soportetecnico').click(function(event){
       
       event.preventDefault();
       $('#acordeon').accordion({'active':2}) 
    });    
    
    $('#viasdecontacto').click(function(event){
       
       event.preventDefault();
       $('#acordeon').accordion({'active':2}) 
    });  
    
    
    
    //Ya dentro de la página de ayuda si le damos a opcion ayuda del menu principal 
    //directamente cargamos en acordeon la opcion de primeros pasos de la ayuda.
    $('#ayuda').click(function(event){
        event.preventDefault();
        $('#acordeon').accordion({'active':0}); 
    });
    // SI LE DOY A LA OPCION DE AYUDA DE PIE DE PAGINA DENTRO DE LA PAGINA DE AYUDA CARGA 
    //DIRECTAMENTE PRIMEROS PASOS
    $('#ayudapiedepagina').click(function(event){
        event.preventDefault();
        $('#acordeon').accordion({'active':0});
    });
    
    
 
  
  
  
  
// PONER EN ROJO Y SUBRAYADO LA OPCION DE AYUDA SI EN LA RUTA ESTAMOS EN LA OPCION DE AYUDA
    if(window.location.href==="http://proyecto-symfony.com.devel/ayuda"){
        $('#ayuda').css('color','red')
                   .css('text-decoration','underline');
    }else{
        $('#ayuda').css('color','#C89C70')
                   .css('text-decoration','none');
    }
    
       
    
    // ABRIR VENTANITA DE ABOUT CUANDO PULSEMOS EN ACERCA DE 
    $('#about').click(function(){
       window.open('http://proyecto-symfony.com.devel/ayuda-sobre-aplicacion',"","width=550,height=700,top=0,left=500");
       
    });
    // CERRAR VENTANITA DE ACERCA DE CUANDO DEMOS A CERRAR
    $('.cerrarabout').click(function(){
        window.close();
    });
    
    
    
    //Scroll hacia arriba 
    
    $('.subir').click(function(event){
        event.preventDefault();
       $('html,body').animate({
           scrollTop:0
       },1000);
       
    });
    // ICONO DE SUBIR QUE ESTA ABAJO DEL TODO A LA DERECHA,LE CAMBIO CUANDO TENEMOS EL CURSOR ENCIMA
    //DEL ICONO SE CAMBIA LA IMAGEN POR OTRA DE OTRO COLOR Y TIENE UNA PEQUEÑA ANIMACION
    
    $('.subir img').mouseover(function(){
        $(this).attr('src','assets/img/flecha_arriba2.jpg')
                .animate({'marginTop':'-50%'},1000);
    });
    
    $('.subir img').mouseleave(function(){
        $(this).attr('src','assets/img/flecha_arriba.jpg')
                .animate({'marginTop':'-28%'},1000);
    });
    
   // Al darle a salir para cerrar sesion evento click para que pregunte si deseas salir
    $("#cerrarsesion").click(function(){
       var respuesta=confirm('¿Quieres cerrar sesion?');
       if(respuesta){
           $('body').load('http://proyecto-symfony.com.devel/logout');
           $('#title').html('GESTOR DE TAREAS');
       }
    });
   
   
});