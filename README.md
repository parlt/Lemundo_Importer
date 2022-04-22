---
title: README

---

# Lemundo_Importer

## General

The purpose of this module is to import json data in the magento database. The import/ update can be started via a
console command. The json file  must be stored in the var folder. Required command option `--json_path=XXX`.

Command example `bin/magento lemundo:importer:importjsondata--json_path=XXX`.

## Overview

#### ``Kinds of imported data``

There are three types of data which are imported.

#### ``Productimages``

The product images are retrieved from the original source and stored in the file system.

#### ``Categories``

The categories are created according to the data to be imported. The original category id is stored in the category
attribute `lemundo_legacy_category_id`.

#### ``Products``

The products are imported according to the original data. The products are assigned to the `Landingpage Products`
attribute set. The following product attributes are assigned to the attribute set:  
`lemundo_product_features`, `lemundo_product_application`, `lemundo_landingpage_relevant`, `lemundo_legacy_product_id`

#### ``Importer impact on Magento``

The importer don't have any impact on the Magento core as it is independent.

## Configuration

| tab | section | group   | field |
|:----|:--------|:--------|:------|
| lemundo | lemundo_importer | general | image_url_prefix |
| lemundo | lemundo_importer | general | json_start_index |
| lemundo | lemundo_importer | general | landing_page_attributeset_name |

### ``\Lemundo\Importer\Config\DefaultConfig``

- Contains importer configuration fields

## Acl

| id | title | parent   |
|:----|:--------|:--------|
| Lemundo_Importer::importer_config | Lemundo Importer Config | Magento_Backend::admin |

## Preferences

| source-class                                       | custom-class                                         |
|:---------------------------------------------------|:-----------------------------------------------------|
| Lemundo\Importer\Api\ImportProcessorPoolInterface  | Lemundo\Importer\Processor\ImportProcessorPool       |
| Lemundo\Importer\Api\ImportServiceInterface        | Lemundo\Importer\Service\ImportService               |
| Lemundo\Importer\Api\ProductMapperPoolInterface    | Lemundo\Importer\Mapper\ProductMapperPool            |

## ImportProcessorPool processors

| name              | processor                                               |
|:------------------|:--------------------------------------------------------|
| ImageProcessor    | Lemundo\Importer\Processor\Import\ImageProcessor        |
| CategoryProcessor | Lemundo\Importer\Processor\Import\CategoryProcessor     |
| ProductProcessor  | Lemundo\Importer\Processor\Import\ProductProcessor      |

## Virtual types

| name                                                        | type                                            |
|:------------------------------------------------------------|:------------------------------------------------|
| Lemundo\Importer\Mapper\Virtual\ProductPreSaveMapperPool    | Lemundo\Importer\Mapper\ProductMapperPool       |
| Lemundo\Importer\Mapper\Virtual\ProductPostSaveMapperPool   | Lemundo\Importer\Mapper\ProductMapperPool       |

## ProductPreSaveMapperPool mapper

| name               | mapper                                                            |
|:------------------ |:------------------------------------------------------------------|
| attributeMapper    | Lemundo\Importer\Mapper\Product\PreSave\ProductAttributeMapper    |
| CategoryProcessor  | Lemundo\Importer\Mapper\Product\PreSave\ProductImagesMapper       |
| ProductProcessor   | Lemundo\Importer\Mapper\Product\PreSave\ProductTaxMappe           |

## ProductPostSaveMapperPool mapper

| name                 | mapper                                                           |
|:------------------- |:------------------------------------------------------------------|
| categoriesMapper    | Lemundo\Importer\Mapper\Product\PostSave\ProductCategoriesMapper  |
| stockMapper         | Lemundo\Importer\Mapper\Product\PostSave\ProductStockMapper       |

## Services

### ``\Lemundo\Importer\Service\ImportService``

Sets the area code, unserialize json and calls the importProcessorPool to start the import process.

## Interfaces

### ``\Lemundo\Importer\Api\ImportServiceInterface``

``\Lemundo\Importer\Processor\ImportProcessorPool::execute`` calls all import processors `ImageProcessor`
, `CategoryProcessor`, `ProductProcessor`.

### ``\Lemundo\Importer\Api\ImportProcessorPoolInterface``

``\Lemundo\Importer\Service\ImportService::execute`` start the import process.

### ``\Lemundo\Importer\Api\ImportProcessorInterface``

``\Lemundo\Importer\Processor\Import\ImageProcessor::process`` fetch and store remote images.

``\Lemundo\Importer\Processor\Import\CategoryProcessor::process`` import category data.

``\Lemundo\Importer\Processor\Import\ProductProcessor::process``import product data.

### ``\Lemundo\Importer\Api\ProductMapperInterface``

``\Lemundo\Importer\Mapper\Product\PreSave\ProductTaxMapper::map``

``\Lemundo\Importer\Mapper\Product\PreSave\ProductAttributeMapper::map`` map and assign necessary attributes.

``\Lemundo\Importer\Mapper\Product\PostSave\ProductCategoriesMapper::map`` map and assign categories.

``\Lemundo\Importer\Mapper\Product\PostSave\ProductStockMapper::map`` map and assign stock data.

``\Lemundo\Importer\Mapper\Product\PreSave\ProductImagesMapper::map`` map and assign images.

## Processors

### ``\Lemundo\Importer\Processor\Import\ImageProcessor``

Fetches and stores images, creates directories in pub/media linked to the image path, uses GuzzleHttp to fetch images,
stores image data.

### ``\Lemundo\Importer\Processor\Import\CategoryProcessor``

Extracts categories and imports them, stores the original category id in `lemundo_legacy_category_id` category
attribute.

### ``\Lemundo\Importer\Processor\Import\ProductProcessor``

Fetch and import product data, products are added with the attributeset `Landingpage Products`, calls
the `ProductPreSaveMapperPool` and `ProductPostSaveMapperPool`.

## Mappers

### ``\Lemundo\Importer\Mapper\Product\PreSave\ProductTaxMapper``

Maps tax class id.

### ``\Lemundo\Importer\Mapper\Product\PreSave\ProductAttributeMapper``

Maps product attribute data, adds `lemundo_landingpage_relevant` flag and stores original product id
in `lemundo_legacy_product_id`.

### ``\Lemundo\Importer\Mapper\Product\PostSave\ProductCategoriesMapper``

Maps product / category relation.

### ``\Lemundo\Importer\Mapper\Product\PostSave\ProductStockMapper``

Maps product stock data adds qty value.

### ``\Lemundo\Importer\Mapper\Product\PreSave\ProductImagesMapper``

Maps product media gallery data adds image if it doesn't exist, images with pos 1 are mapped as `small_image`
, `thumbnail`, `base_image`.

## Pools

### ``\Lemundo\Importer\Mapper\ProductMapperPool``

Calls all mappers, has two Virtual types `\Lemundo\Importer\Mapper\Virtual\ProductPreSaveMapperPool`, `\Lemundo\Importer\Mapper\Virtual\ProductPostSaveMapperPool`.

## Console

### ``\Lemundo\Importer\Console\Command\ImportJsonData``

Fetches the json file content and starts the import process. Calls `\Lemundo\Importer\Service\ImportService`, required command option `--json_path=XXX`.

Command example `bin/magento lemundo:importer:importjsondata--json_path=XXX`.

## Plugins

### lemundo_catalog_magento_catalog_model_category_dataprovider_plugin

`Lemundo\Importer\Plugin\Magento\Catalog\Model\Category\DataProviderPlugin::afterPrepareMeta` - intercepts
`Magento\Catalog\Model\Category\DataProvider::prepareMeta`. The intercepted method prepares the meta data for the
category view. Adds the custom category attribute `lemundo_legacy_category_id` in meta data..

## Data Patches

`Lemundo\Importer\Setup\Patch\Data\AddLegacyCategoryIdAttribute` - setup data patch for adding category
attribute `lemundo_legacy_category_id`.

`Lemundo\Importer\Setup\Patch\Data\AddLandingPageProductAttributeSet` - setup data patch for adding attribute
set `Landingpage Products`.

`Lemundo\Importer\Setup\Patch\Data\AddLandingPageProductAttributes` - setup data patch for adding product attributes
`lemundo_product_features`, `lemundo_product_application`, `lemundo_landingpage_relevant`, `lemundo_legacy_product_id`.
