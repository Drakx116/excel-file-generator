<?php


namespace App\Service;


use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class ArticleManager
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * ArticleManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param integer $id
     * @return Article|object|null
     */
    public function getArticleById($id)
    {
        $articleRepo = $this->entityManager->getRepository(Article::class);

        return $articleRepo->findOneBy(array('id' => $id));
    }

}