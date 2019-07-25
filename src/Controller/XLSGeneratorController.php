<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Service\ArticleManager;
use App\Service\InstituteManager;
use App\Service\StockManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as ExcelFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class XLSGeneratorController extends AbstractController
{
    /**
     * @var StockManager $stockManager
     */
    protected $stockManager;

    /**
     * @var ArticleManager $articleManager
     */
    protected $articleManager;

    /**
     * @var InstituteManager $instituteManager
     */
    protected $instituteManager;

    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * XLSGeneratorController constructor.
     * @param StockManager $stockManager
     * @param EntityManagerInterface $entityManager
     * @param ArticleManager $articleManager
     * @param InstituteManager $instituteManager
     */
    public function __construct(StockManager $stockManager, EntityManagerInterface $entityManager, ArticleManager $articleManager, InstituteManager $instituteManager)
    {
        $this->stockManager = $stockManager;
        $this->entityManager = $entityManager;
        $this->articleManager = $articleManager;
        $this->instituteManager = $instituteManager;
    }

    /**
     * Tries file generation and returns a status message
     * @Route("/xls-generator", name="xls-generator")
     */
    public function generateXLSFile()
    {
        $message = null;
        $error = null;
        $status = $this->createXLSFile();
        switch($status)
        {
            case 2:
                $message = "File created.";
                break;

            case 1:
                $error = "This file is already opened. Please close it to create a new one.";
                break;

            default:
                $error = "Something happened. No file created.";
        }
        return $this->render('xls_generator/index.html.twig', [
            'message' => $message,
            'error'   => $error
        ]);
    }

    /**
     * Tries to create a basic XLS File
     * @return integer
     */
    public function createXLSFile()
    {
        $spreadsheet = new Spreadsheet();
        try
        {
            $filledSpreadsheet = $this->writeXLSFile($spreadsheet);
            if(!$this->saveXLSFile($filledSpreadsheet))
            {
                return 1;
            }
            return 2;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    /**
     * Write columns into the spreadsheet
     * @param Spreadsheet $spreadsheet
     * @return Spreadsheet
     * @throws Exception
     */
    public function writeXLSFile($spreadsheet)
    {
        $emptySheet = $spreadsheet->getActiveSheet();
        $sheet = $this->fillXLSFile($emptySheet);
        return $spreadsheet;
    }

    /**
     * Writes file's body
     * @param $sheet
     * @return mixed
     */
    public function fillXLSFile($sheet)
    {
        $unlinkedStocks = $this->stockManager->getAllStocks();

        $stocks = $this->getGlobalStocks($unlinkedStocks);

        $articleReferences = array();

        foreach($stocks as $stock)
        {
            array_push($articleReferences, $stock["article"]->getReference());
        }

        $headerInfos = $this->writeHeader($sheet, $articleReferences);
        $sheet = $headerInfos["sheet"];
        $columnCounter = $headerInfos["columnCounter"];
        $sheet = $this->writeBody($sheet, $stocks, $columnCounter);

        // $sheet->setCellValue('A2', 'Hello World !');
        return $sheet;
    }

    /**
     * Writes file header with category name
     * @param $sheet
     * @param $references
     * @return mixed
     */
    public function writeHeader($sheet, $references)
    {
        // Header File
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => 'FF0000']
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF505050'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        // Data labels
        $categories = array( "Ndist", "Raison", "Demandeur", "Adress1", "Adress2", "Adress3", "CP", "Ville", "Tel" );

        // Merges Categories and References Arrays to create the header
        $indexes = array_merge($categories, array_unique($references));

        for($i = 0; $i < count($indexes); $i++)
        {
            $columnName = $this->getColumnName($i+1);
            $cellCoordinate = $columnName . "1";
            $sheet->setCellValue($cellCoordinate, $indexes[$i]);
            $sheet->getColumnDimension($columnName)->setAutoSize(true);
            $sheet->getStyle($cellCoordinate)->applyFromArray($headerStyle);
        }

        return array(
            'sheet' => $sheet,
            'columnCounter' => count($indexes)
        );
    }

    public function writeBody($sheet, $stocks, $columnCounter)
    {
        $bodyStyle = [
            'font' => [
                "size" => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];


        $currentLineIndex = 2;
        foreach($stocks as $stock)
        {
            $data = $this->getStockData($stock);

            for($i = 0; $i < 9; $i++) // Institute Data
            {
                $currentCellCoordinate = $this->getLetterByIndex($i+1) . ($currentLineIndex);
                $sheet->setCellValue($currentCellCoordinate, $data[$i]);
            }

            for($i = 9; $i < $columnCounter; $i++)
            {
                $headerCellCoordinate = $this->getLetterByIndex(($i) + 1) . (1);
                $headerCellValue = strval($this->getCellValue($sheet,  $headerCellCoordinate));

                $currentCellCoordinate = $this->getLetterByIndex($i+1) . ($currentLineIndex);

                $stockValue = $this->setStockByReference($stock, $headerCellValue);

                $sheet->setCellValue($currentCellCoordinate, $stockValue);
                $sheet->getStyle($currentCellCoordinate)->applyFromArray($bodyStyle);

            }

            $currentLineIndex++;
        }

        return $sheet;
    }

    /**
     * @param $stock
     * @param $headerCellValue
     * @return int
     */
    public function setStockByReference($stock, $headerCellValue)
    {
        return ($headerCellValue === $stock["article"]->getReference()) ? $stock["stock"] : 0;
    }

    /**
     * @param $sheet
     * @param $cellCoordinate
     * @return string
     */
    public function getCellValue($sheet, $cellCoordinate)
    {
        return $sheet->getCell($cellCoordinate)->getValue();
    }

    /**
     * Saves the XLS File into the /public/xls/ dir
     * @param Spreadsheet $spreadsheet
     * @return string
     */
    public function saveXLSFile($spreadsheet)
    {
        try
        {
            $writer = new ExcelFile($spreadsheet);
            $writer->setPreCalculateFormulas(true);
            $writer->save("xls/test.xlsx");
            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param Stock array
     * @return array
     */
    public function getGlobalStocks($stocks)
    {
        $globalStocks = array();

        foreach($stocks as $stock)
        {
            $articleId = $stock->getArticleId();
            $instituteId = $stock->getInsituteId();

            $article = $this->articleManager->getArticleById($articleId);
            $institute = $this->instituteManager->getInstituteById($instituteId);

            array_push($globalStocks, array("article" => $article, "institute" => $institute, "stock" => $stock->getStock()));
        }

        return $globalStocks;
    }

    /**
     * @param Stock $stock
     * @return array
     */
    public function getStockData($stock)
    {
        return array(
            $stock["institute"]->getName(),
            $stock["institute"]->getReason(),
            $stock["institute"]->getReceiver(),
            $stock["institute"]->getAddress1(),
            $stock["institute"]->getAddress2(),
            $stock["institute"]->getAddress3(),
            $stock["institute"]->getPostCode(),
            $stock["institute"]->getCity(),
            $stock["institute"]->getPhoneNumber(),
            $stock["article"]->getReference(),
            $stock["stock"]
        );
    }

    /**
     * Gets the correct column number
     * @param $columnNumber
     * @return string|null
     */
    public function getColumnName($columnNumber)
    {
        // Letters
        $firstLetter = 0;
        $secondLetter = 0;
        $thirdLetter = 0;

        // Indexes
        $firstLetterIndex = 0;
        $secondLetterIndex =  0;
        $thirdLetterIndex = 0;

        // Checks column name letters number
        $case = 0;

        $steps = array(
            "third"  => false,
            "second" => false,
            "first"  => false
        );

        // Find column name pattern (1, 2 or 3 letters)
        while(702 < $columnNumber && $columnNumber <= 17576)
        {
            $columnNumber -= 676; // 26^2
            $thirdLetterIndex++;
            $steps["third"] = true;
        }

        while(26 < $columnNumber && $columnNumber <= 702)
        {
            $columnNumber -= 26; // 26^1
            $secondLetterIndex++;
            $steps["second"] = true;
        }

        while(0 < $columnNumber && $columnNumber <= 26)
        {
            $columnNumber -= 1; // 26^0
            $firstLetterIndex++;
            $steps["first"] = true;
        }

        // Check pattern letters number
        foreach($steps as $step)
        {
            if($step === true)
            {
                $case++;
            }
        }

        // Get single letter by index
        switch($case)
        {
            case 3:
                $thirdLetter = $this->getLetterByIndex($thirdLetterIndex);
                $secondLetter = $this->getLetterByIndex($secondLetterIndex);
                $firstLetter = $this->getLetterByIndex($firstLetterIndex);
                break;

            case 2:
                $secondLetter = $this->getLetterByIndex($secondLetterIndex);
                $firstLetter = $this->getLetterByIndex($firstLetterIndex);
                break;

            case 1:
                $firstLetter = $this->getLetterByIndex($firstLetterIndex);
                break;
        }

        // Set column name
        $letters = array($thirdLetter, $secondLetter, $firstLetter);
        $columnName = null;

        foreach($letters as $letter)
        {
            if(!(is_integer($letter)))
            {
                $columnName .= $letter;
            }
        }

        return $columnName;
    }

    /**
     * Gets the letter matching the given index
     * @param $index
     * @return string | null
     */
    public function getLetterByIndex($index)
    {
        if($index)
        {
            $letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

            return $letters[$index-1];
        }
        return null;
    }

}