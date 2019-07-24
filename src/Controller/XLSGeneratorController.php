<?php

namespace App\Controller;

use App\Service\StockManager;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
     * XLSGeneratorController constructor.
     * @param StockManager $stockManager
     */
    public function __construct(StockManager $stockManager)
    {
        $this->stockManager = $stockManager;
    }

    /**
     * @Route("/xls-generator", name="xls-generator")
     */
    public function generateXLSFile()
    {
        dump($this->createXLSFile());
        return $this->render('xls_generator/index.html.twig');
    }

    /**
     * Tries to create a basic XLS File
     * @return bool
     */
    public function createXLSFile()
    {
        $spreadsheet = new Spreadsheet();
        try
        {
            $filledSpreadsheet = $this->writeXLSFile($spreadsheet);
            $this->saveXLSFile($filledSpreadsheet);
            return true;
        }
        catch(Exception $e)
        {
            return false;
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
        $sheet = $this->writeHeader($sheet);
        $sheet = $this->writeBody($sheet);

        // $sheet->setCellValue('A2', 'Hello World !');
        return $sheet;
    }

    /**
     * Writes file header with category name
     * @param $sheet
     * @return mixed
     */
    public function writeHeader($sheet)
    {
        $headerStyle = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        $category = array( "Ndist", "Raison", "Demandeur", "Adress1", "Adress2", "Adress3", "CP", "Ville", "Tel" );

        for($i = 1; $i < count($category); $i++)
        {
            $columnName = $this->getColumnName($i);
            $cellName = $columnName."1";
            $sheet->setCellValue($cellName, $category[$i-1]);
            $sheet->getStyle($cellName)->applyFromArray($headerStyle);
        }

        return $sheet;
    }

    public function writeBody($sheet)
    {
        $stocks = $this->stockManager->getAllStocks();
        // dump($stocks);
        return $sheet;
    }

    /**
     * Saves the XLS File into the /public/xls/ dir
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function saveXLSFile($spreadsheet)
    {
        $writer = new ExcelFile($spreadsheet);
        $writer->setPreCalculateFormulas(true);
        $writer->save("xls/test.xlsx");
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