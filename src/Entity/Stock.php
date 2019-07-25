<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockRepository")
 */
class Stock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $insitute_id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToMany(targetEntity="Institute")
     * @ORM\JoinColumn(name="institute_id", referencedColumnName="id")
     */
    private $article_id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToMany(targetEntity="Article")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInsituteId(): ?int
    {
        return $this->insitute_id;
    }

    public function setInsituteId(int $insitute_id): self
    {
        $this->insitute_id = $insitute_id;

        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    public function setArticleId(int $article_id): self
    {
        $this->article_id = $article_id;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
