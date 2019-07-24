<?php


namespace App\Service;


use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

class StockManager
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * StockManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =  $entityManager;
    }

    public function getAllStocks()
    {
        // Gets Stocks Repository
        $stocksRepository = $this->entityManager->getRepository(Stock::class);

        // Gets all stocks
        return $stocksRepository->findAll();
    }

}