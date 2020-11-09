<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;// PAra poder sacar las tareas en coleccion
use Doctrine\Common\Collections\Collection;// PAra poder sacar las tareas en colleccion
use Symfony\Component\Validator\Constraints as Assert;// Libreria Validacion 

use Symfony\Component\Security\Core\User\UserInterface; //Libreria para encriptar password y login

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(
     *               pattern="/[a-zA-Z ]+/",
     *               message="El nombre '{{ value }}' no es valido"
     *              )
     */ 
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname", type="string", length=200, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(
     *               pattern="/[a-zA-Z ]+/",
     *               message="Los apellidos '{{ value }}' no son validos"
     *              )
     */
    private $surname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\Email(
     *              message="El email '{{ value }}' no es valido."
     *                           
     * )
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *              message="La contraseña no puede estar vacía."
     * )
     */
    private $password;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="fraseseguridad", type="string", length=200, nullable=true)
     * @Assert\NotBlank(
     *                  message="Para cambiar contraseña tienes que introducir tu frase de seguridad."
     * )
     */
    
    
    private $fraseseguridad;
    
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="enterprise", type="string", length=200, nullable=true)
     * 
     * @Assert\Regex(
     *               pattern="/[a-zA-Z ]+/",
     *               message="Los apellidos '{{ value }}' no son validos"
     *              )
     */
    
    private $enterprise;
    

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     
     */
    private $createdAt;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task",mappedBy="user")
     */
    private $tasks;

    
    public function __construct(){
        $this->tasks=new ArrayCollection();
    }
    
    
    public function getId()
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * @return Collection|Task[]
     */
    public function getTasks():Collection{
        return $this->tasks;
    }

    // Metodos que me obliga la interface UserInterface para implementar encriptacion
    public function getUsername(){
        return $this->email;
    }
    
    public function getSalt(){
        return null;
    }
    public function getRoles(){
        return array('ROLE_USER');
    }
    
    public function eraseCredentials(){
        
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }

    public function getFraseseguridad(): ?string
    {
        return $this->fraseseguridad;
    }

    public function setFraseseguridad(?string $fraseseguridad): self
    {
        $this->fraseseguridad = $fraseseguridad;

        return $this;
    }

    public function getEnterprise(): ?string
    {
        return $this->enterprise;
    }

    public function setEnterprise(?string $enterprise): self
    {
        $this->enterprise = $enterprise;

        return $this;
    }

}
