<?php


namespace App\Service;


use App\Entity\Institute;
use Doctrine\ORM\EntityManagerInterface;

class InstituteManager
{
    /**
     * @var EntityManagerInterface $entityManager;
     */
    protected $entityManager;

    /**
     * InstituteManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     * @return Institute|object|null
     */
    public function getInstituteById($id)
    {
        $instituteRepo = $this->entityManager->getRepository(Institute::class);

        return $instituteRepo->findOneBy(array("id" => $id));
    }
}