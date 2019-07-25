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

    /**
     * @return Stock[]|object[]
     */
    public function getAllStocks()
    {
        // Gets Stocks Repository
        $stocksRepository = $this->entityManager->getRepository(Stock::class);

        $stocks = $stocksRepository->findAll();

        return $stocks;
    }

}