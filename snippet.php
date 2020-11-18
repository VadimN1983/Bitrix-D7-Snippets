<?php

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

/**
 * Add or update list value (List property)
 * 
 * @param int    $property_id
 * @param string $property_value
 * @return int
 */
function valuePropertyEnum($property_id = 0, $property_value = '')
{
    $cacheId = md5(serialize([$property_id, $property_value]));
    $cacheTime = 14400;
    $cachePath = '/EnumList_'.__FUNCTION__.'/prop_' . $property_id;

    $cache = \Bitrix\Main\Data\Cache::createInstance();
    if($cache->initCache($cacheTime, $cacheId, $cachePath))
    {
        $prop['ID'] = $cache->GetVars()['ID'];
    }
    elseif($cache->startDataCache())
    {
        $prop = \Bitrix\Iblock\PropertyEnumerationTable::getList([
            'filter' => [
                'PROPERTY_ID' => $property_id,
                'VALUE' => $property_value
            ],
            'select' => ['ID']
        ])->fetch();
        if (empty($prop['ID']))
        {
            $prop['ID'] = \Bitrix\Iblock\PropertyEnumerationTable::add([
                'PROPERTY_ID' => $property_id,
                'VALUE' => $property_value,
                'XML_ID' => $property_value
            ])->getId();
        }

        $cache->endDataCache(['ID' => $prop['ID']]);
    }
    return $prop['ID'];
}

/**
 * Add or update position price
 *
 * @param int    $item_id  Product ID
 * @param int    $price    Product price
 * @param string $currency Price currency (USD|RUB|EUR etc.)
 * @return int Price ID
 */
function itemPrice($item_id = 0, $price = 0, $currency = '')
{
    $cacheId   = md5(serialize([$item_id, $currency]));
    $cacheTime = 14400;
    $cachePath = '/PriceList_'.__FUNCTION__.'/price_' . $item_id;

    $arFields = [
        'PRODUCT_ID'       => $item_id,
        'CATALOG_GROUP_ID' => 1,
        'PRICE'            => $price,
        'CURRENCY'         => $currency
    ];

    $cache = \Bitrix\Main\Data\Cache::createInstance();
    if($cache->initCache($cacheTime, $cacheId, $cachePath))
    {
        $priceData['ID'] = $cache->GetVars()['ID'];
    }
    elseif($cache->startDataCache())
    {
        $priceData = \Bitrix\Catalog\Model\Price::getList([
            'filter' => ['=PRODUCT_ID' => $item_id],
            'select' => ['ID']
        ])->fetch();
        $cache->endDataCache(['ID' => $priceData['ID']]);
    }

    if(!empty($priceData))
        \Bitrix\Catalog\Model\Price::update($priceData['ID'], $arFields);
    else
        \Bitrix\Catalog\Model\Price::add($arFields);

    return $priceData['ID'];
}

/**
 * Add or update position store
 *
 * @param int $item_id  Product ID
 * @param int $store_id Store ID
 * @param int $amount   Product amount
 * @return int          Store record ID
 */
function itemStore($item_id = 0, $store_id = 0, $amount = 0)
{
    $cacheId   = md5(serialize([$item_id, $store_id]));
    $cacheTime = 14400;
    $cachePath = '/StoreList_'.__FUNCTION__.'/store_' . $item_id;

    $arFields = [
        "AMOUNT"     => $amount,
        "PRODUCT_ID" => $item_id,
        "STORE_ID"   => $store_id
    ];

    $cache = \Bitrix\Main\Data\Cache::createInstance();
    if($cache->initCache($cacheTime, $cacheId, $cachePath))
    {
        $storeData['ID'] = $cache->GetVars()['ID'];
    }
    elseif($cache->startDataCache())
    {
        $storeData = \Bitrix\Catalog\StoreProductTable::getList([
            'filter' => [
                '=PRODUCT_ID' => $item_id,
                'STORE.ACTIVE' => 'Y',
                'STORE_ID' => $store_id
            ],
            'select' => ['ID'],
        ])->fetch();
        $cache->endDataCache(['ID' => $storeData['ID']]);
    }

    if(!empty($storeData))
        \Bitrix\Catalog\StoreProductTable::update($storeData['ID'], $arFields);
    else
        \Bitrix\Catalog\StoreProductTable::add($arFields);

    return $storeData['ID'];
}
