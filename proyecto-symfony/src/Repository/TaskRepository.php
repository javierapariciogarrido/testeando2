<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }
    
    
    
   
    //METODO QUE PAGINA,LE PASAMOS LA CONSULTA EN QUERYBUILDER,LA PAGINA ACTUAL Y EL NUMERO DE
   //REGISTROS DE TAREAS QUE QUEREMOS MOSTRAR POR PÁGINA 
   /*
    public function paginate($dql, $page = 1, $limit = 3){
        $paginator = new Paginator($dql);
        
        $paginator->getQuery()->fetch_object()    
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit
            
        return $paginator;
        
}
*/
// ESTE METODO ES EL QUE REALIZA LA CONSULTA CON QUERYBUILDER Y DENTRO DEL MISMOM INVOCAMOS 
//AL METODO DE ARRIBA PASANDOLE LA CONSULTA QUE DEVOLVEMOS AL FINAL DEL METODO
/*
public function getAllPers($currentPage = 1, $limit = 3,$enterprise)
{
    // Create our query
    $queryalltasks = $this->createQueryBuilder('t')
            ->andWhere("t.acabada = :acabada")
            ->setParameter('acabada','NO')
            ->orderBy('t.id','DESC')
            ->getQuery();
    $resultset=$queryalltasks->execute(); //EN RESULTSET TENGO TODAS LAS TAREAS
    
    // Lógica para filtrar las tareas cuya empresa sea la misma de el usuario logado
    // Array usado para almacenar todas las tareas de de la misma empresa  
        $tareastodosempleados=[];
        $indice=0;
        foreach ($resultset as $task){
            //Compruebo de cada tarea la empresa del usuario que la creo,si es igual a la 
            //empresa de la persona que esta logada,almacena esa tarea en el array
            //tareastodosempleados
            if($task->getUser()->getEnterprise()== $enterprise && $enterprise!=''){
                $tareastodosempleados[$indice]=$task;
                $indice++;
            }
        }
    
    // Metemos en $query el array de todas las tareas cuyo usuario pertenezca a la empresa 
        //del usuario logado
    
    $query=$tareastodosempleados;    
    
    $paginator = $this->paginate($query, $currentPage, $limit);
    
    return array('paginator' => $paginator, 'query' => $query);
}
*/


    
    
    
    /*
    public function paginationtasks($pageSize=5,$currentPage=1,$id,$acabada){
        // Consulta echa con dql
        $em=$this->getDoctrine()->getManager();
        
        
        
        return $paginator;
    }
    */
    
    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
