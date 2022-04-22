<?php
declare(strict_types=1);

namespace Lemundo\Importer\Console\Command;

use Lemundo\Importer\Api\ImportServiceInterface;
use Lemundo\Importer\Exception\ImportException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportJsonData extends Command
{
    const JSON_PATH = 'json_path';

    private ImportServiceInterface $importServiceInterface;

    private DirectoryList $directoryList;

    private File $driverFile;

    public function __construct(
        ImportServiceInterface $importServiceInterface,
        DirectoryList $directoryList,
        File $driverFile
    ) {
        $this->importServiceInterface = $importServiceInterface;
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lemundo:importer:importjsondata')
            ->setDescription('Imports landing page, products, category and images.')
            ->addOption(self::JSON_PATH, null, InputOption::VALUE_REQUIRED, 'Json path');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonPath = $input->getOption(self::JSON_PATH);
        if (empty($jsonPath)) {
            $output->writeln('Json path can not be empty.');
            return -1;
        }

        try {
            $content = $this->getFileContent($jsonPath);
            $this->importServiceInterface->execute($content);
        } catch (ImportException $e) {
            $output->writeln($e->getMessage());
            return -1;
        }
        return 0;
    }

    private function getFileContent(string $file): string
    {
        $path = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . "/" . $file;
        return $this->driverFile->fileGetContents($path);
    }
}

