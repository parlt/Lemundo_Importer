<?php
declare(strict_types=1);

namespace Lemundo\Importer\Mapper\Product\PreSave;

use JetBrains\PhpStorm\Pure;
use Lemundo\Importer\Api\ProductMapperInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File as FileSystemIo;
use Psr\Log\LoggerInterface;

class ProductImagesMapper implements ProductMapperInterface
{
    private FileSystemIo $fileSystemIo;

    private DirectoryList $directoryList;

    public function __construct(DirectoryList $directoryList, FileSystemIo $fileSystemIo)
    {
        $this->fileSystemIo = $fileSystemIo;
        $this->directoryList = $directoryList;
    }

    /**
     * @throws FileSystemException
     */
    public function map(array $data, ProductInterface $product): ProductInterface
    {

        if (empty($data['media_gallery'])) {
            return $product;
        }

        $existingImages = [];
        foreach ($product->getMediaGalleryImages()->toArray()['items'] as $existingImage) {
            $imgName = $this->getBasename($existingImage['file']);
            $existingImages[$imgName] = $imgName;
        }
        $legacyMediaGalleryData = $data['media_gallery'];
        foreach ($legacyMediaGalleryData as $mediaItem) {
            $mediaItemImage = $mediaItem['image'];
            $mediaItemImageFileName = $this->getBasename($mediaItemImage);
            if (\array_key_exists($mediaItemImageFileName, $existingImages)) {
                continue;
            }

            $imagePath = $this->getLocalPrefixDirectory() . $this->trimImagePath($mediaItemImage);
            $product->addImageToMediaGallery(
                $imagePath,
                $this->prepareMediaItemData($mediaItem),
                false,
                false
            );
        }

        return $product;
    }

    private function prepareMediaItemData(array $mediaItem): array
    {
        $baseImage = $mediaItem['pos'] === 1;
        $data = !$baseImage
            ? ['image']
            : ['image', 'small_image', 'thumbnail', 'base_image'];

        if (!empty($mediaItem['lab']) && $baseImage) {
            $lab = $mediaItem['lab'];
            return array_merge(
                $data,
                [
                    'base_image_label' => $lab,
                    'thumbnail_image_label' => $lab,
                    'small_image_label' => $lab
                ]
            );
        }
        return $data;
    }

    /**
     * @throws FileSystemException
     */
    private function getLocalPrefixDirectory(): string
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA)
            . DIRECTORY_SEPARATOR
            . 'catalog'
            . DIRECTORY_SEPARATOR
            . 'product'
            . DIRECTORY_SEPARATOR;
    }

    private function getBasename(string $imagePath): string
    {
        $file = $this->fileSystemIo->getPathInfo($imagePath)['basename'];
        return preg_replace('/\_\d+/', '', $file);
    }

    private function trimImagePath(string $imagePath): string
    {
        return ltrim($imagePath, '/');
    }
}
