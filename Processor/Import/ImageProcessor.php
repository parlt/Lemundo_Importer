<?php
declare(strict_types=1);

namespace Lemundo\Importer\Processor\Import;

use GuzzleHttp\ClientFactory as HttpClientFactory;
use Lemundo\Importer\Api\ImportProcessorInterface;
use Lemundo\Importer\Config\DefaultConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File as FileSystemIo;

class ImageProcessor implements ImportProcessorInterface
{
    private DefaultConfig $config;

    private HttpClientFactory $httpClientFactory;

    private DirectoryList $directoryList;

    private FileSystemIo $fileSystemIo;

    public function __construct(
        DefaultConfig $config,
        HttpClientFactory $httpClientFactory,
        DirectoryList $directoryList,
        FileSystemIo $fileSystemIo
    ) {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->directoryList = $directoryList;
        $this->fileSystemIo = $fileSystemIo;
    }

    public function process(array $data): void
    {
        $images = $this->getImages($data);
        foreach ($images as $imagePath) {

            $fullLocalImagePath = $this->getFullLocalImagePath($imagePath);

            if (!\file_exists($fullLocalImagePath)) {
                $directory = $this->getDirectoryPath($imagePath);
                $localDirectory = $this->getLocalDirectoryPath($directory);
                $this->createDirectoryRecursiveIfNotExists($localDirectory);
                $webImagePath = $this->getWebImageUrl($imagePath);
                $imageContent = $this->fetchImage($webImagePath);
                $this->writeImage($fullLocalImagePath, $imageContent);
            }
        }
    }

    private function writeImage(string $fullLocalImagePath, string $data): void
    {
        \file_put_contents($fullLocalImagePath, $data);
    }

    private function fetchImage(string $webImagePath): string
    {
        $client = $this->httpClientFactory->create();
        $response = $client->request('GET', $webImagePath);
        return $response->getBody()->getContents();
    }

    private function getWebImageUrl(string $image): string
    {
        return $this->config->getImageUrlPrefix() . $image;
    }

    private function createDirectoryRecursiveIfNotExists(string $directory)
    {
        if (!\file_exists($directory)) {
            $this->fileSystemIo->mkdir($directory, 0775, true);
        }
    }

    private function getFullLocalImagePath(string $imagePath): string
    {
        return $this->getLocalPrefixDirectory() . $imagePath;
    }

    private function getLocalDirectoryPath(string $director): string
    {
        return $this->getLocalPrefixDirectory()
            . $director;
    }

    private function getLocalPrefixDirectory(): string
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA)
            . DIRECTORY_SEPARATOR
            . 'catalog'
            . DIRECTORY_SEPARATOR
            . 'product'
            . DIRECTORY_SEPARATOR;
    }

    private function getDirectoryPath(string $imagePath): string
    {
        return $this->fileSystemIo->dirname($imagePath);
    }

    private function getImages(array $data): array
    {
        $mediaData = [];
        foreach ($data as $item) {

            if (empty($item['_source']['media_gallery'])) {
                continue;
            }

            $mediaGallery = $item['_source']['media_gallery'];
            foreach ($mediaGallery as $mediaItem) {
                $imagePath = $this->trimImagePath($mediaItem['image']);
                $mediaData[$imagePath] = $imagePath;
            }
        }
        return $mediaData;
    }

    private function trimImagePath(string $imagePath): string
    {
        return ltrim($imagePath, '/');
    }
}
