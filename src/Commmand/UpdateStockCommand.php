<?php

namespace App\Commmand;

use App\Entity\StockItem;
use App\Repository\StockItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UpdateStockCommand extends Command
{

    protected static $defaultName = 'app:update-stock';
    private $projectDir;
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */

    public function __construct($projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {

        $this->setDescription('Update stock records')
            ->addArgument('markup', InputArgument::OPTIONAL, 'percentage markup', 20)
            ->addArgument('process_date', InputArgument::OPTIONAL, 'date of the process', date_create()->format('Y-m-d'));

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $processDate = $input->getArgument('process_date');

        $markup = ($input->getArgument('markup') / 100) + 1;

        $supplierProducts = $this->getCsvRowsAsArray($processDate);

        /** @var StockItemRepository $stockItemRepo */
        $stockItemRepo = $this->entityManager->getRepository(StockItem::class);

        // loop
        foreach ($supplierProducts as $supplierProduct) {

            /** @var StockItem $existingStockItem */
            if($existingStockItem = $stockItemRepo->findOneBy(['itemNumber' => $supplierProduct['item_number']]))
            {
                $this->updateStockItem();
                $existingStockItem->setSupplierCost($supplierProduct['cost']);
                $existingStockItem->setPrice($supplierProduct['cost'] * $markup);
                $this->entityManager->persist($existingStockItem);

                continue;
            }

            $this->createNewStockItem();

        }


    }

    public function createNewStockItem($supplierProduct, $markup){

        $newStockItem = new StockItem();
        $newStockItem->setItemNumber($supplierProduct['item_number']);
        $newStockItem->setItemName($supplierProduct['item_name']);
        $newStockItem->setItemDescription($supplierProduct['description']);
        $newStockItem->setSupplierCost($supplierProduct['cost']);
        $newStockItem->setItemPrice($supplierProduct['cost'] * $markup);
        $this->entityManager->persist($newStockItem);

    }

    public function updateStockItem($supplierProduct, $markup){

        $newStockItem = new StockItem();
        $newStockItem->setItemNumber($supplierProduct['item_number']);
        $newStockItem->setItemName($supplierProduct['item_name']);
        $newStockItem->setItemDescription($supplierProduct['description']);
        $newStockItem->setSupplierCost($supplierProduct['cost']);
        $newStockItem->setItemPrice($supplierProduct['cost'] * $markup);
        $this->entityManager->persist($newStockItem);

    }



    public function getCsvRowsAsArray($processDate)
    {

        $inputFile = $this->projectDir . '/public/supplier-inventory-files/' . $processDate . '.csv';

        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($inputFile), 'csv');

    }

}
