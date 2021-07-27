<?php

namespace App\Commmand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStockCommand extends Command
{

    protected static $defaultName = 'app:update-stock';
    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;

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

        dd($this->projectDir);

        // convert csv content into php

        // loop

        //update if record found in DB

        //create a new records if matching records not found in the DB


    }

}