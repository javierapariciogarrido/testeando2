<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table(name="tasks", indexes={@ORM\Index(name="fk_task_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="¡¡El campo nombre de tarea no puede estar vacio!!"
     * )
     * 
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     * @Assert\NotBlank(message="¡¡El Contenido de la tarea no puede estar vacio!!")
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(name="priority", type="string", length=20, nullable=true)
     */
    private $priority;

    /**
     * @var int|null
     *
     * @ORM\Column(name="hours", type="integer", nullable=true)
     * @Assert\NotBlank
     * @Assert\Positive(message="¡¡El valor de las horas debe ser positivo!!"
     * 
     * )
     * 
     */
    private $hours;
    
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="fechafin", type="string", length=20, nullable=true)
     * @Assert\NotBlank
     * 
     * 
     */
    
    private $fechafin;
        
    /**
     * @var string|null
     *
     * @ORM\Column(name="acabada", type="string", length=5, nullable=true)
     */
    private $acabada;
    
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    
        
    private $createdAt;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="tasks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(?int $hours): self
    {
        $this->hours = $hours;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFechafin(): ?string
    {
        return $this->fechafin;
    }

    public function setFechafin(?string $fechafin): self
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    public function getAcabada(): ?string
    {
        return $this->acabada;
    }

    public function setAcabada(?string $acabada): self
    {
        $this->acabada = $acabada;

        return $this;
    }


}
