<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\UserGroupTable;

$productIds = [];
foreach ($arResult['ITEMS'] as $product) {
    $productIds[] = $product['ID'];
}

//$authorStatusesIblock = \Bitrix\Iblock\Iblock::wakeUp(STATUSES_IBLOCK_ID)->getEntityDataClass();
//
//$reviewIblock = \Bitrix\Iblock\Iblock::wakeUp(REVIEWS_IBLOCK_ID)->getEntityDataClass();
//$reviews = $reviewIblock::query()
//    ->setSelect(['NAME', 'PRODUCT_ID' => 'PRODUCT.VALUE'])
//    ->setFilter([
//        'ACTIVE' => 'Y',
//        'PRODUCT_ID' => $productIds,
//        'STATUS.CODE' => ALLOWED_AUTHOR_STATUS_CODE,
//        'GROUP.GROUP_ID' => ALLOWED_AUTHOR_GROUP_ID,
//        'USER.ACTIVE' => 'Y',
//        'STATUS.ACTIVE' => 'Y',
//    ])
//    ->registerRuntimeField(
//        null,
//        new \Bitrix\Main\Entity\ReferenceField(
//            'USER',
//            Bitrix\Main\UserTable::class,
//            Join::on('this.AUTHOR.VALUE', 'ref.ID')
//        )
//    )
//    ->registerRuntimeField(
//        null,
//        new \Bitrix\Main\Entity\ReferenceField(
//            'STATUS',
//            $authorStatusesIblock,
//            Join::on('this.USER.' . AUTHOR_STATUS_FIELD, 'ref.ID')
//        )
//    )
//    ->registerRuntimeField(
//        null,
//        new \Bitrix\Main\Entity\ReferenceField(
//            'GROUP',
//            \Bitrix\Main\UserGroupTable::class,
//            Join::on('this.AUTHOR.VALUE', 'ref.USER_ID')
//        )
//    )
//    ->fetchAll();

$userIds = \Bitrix\Main\UserTable::query()
    ->setSelect(['ID'])
    ->setFilter([
        'ACTIVE' => 'Y',
        AUTHOR_STATUS_FIELD => ALLOWED_AUTHOR_STATUS_ID,
        'GROUP.GROUP_ID' => ALLOWED_AUTHOR_GROUP_ID
    ])
    ->registerRuntimeField(
        null,
        new \Bitrix\Main\Entity\ReferenceField(
            'GROUP',
            UserGroupTable::class,
            Join::on('this.ID', 'ref.USER_ID')
        )
    )
    ->fetchAll();

$allowedUserIds = [];
foreach ($userIds as $userId) {
    $allowedUserIds[] = $userId['ID'];
}

$obReviews = CIBlockElement::GetList(
    arFilter: [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => REVIEWS_IBLOCK_ID,
    'PROPERTY_PRODUCT.ID' => $productIds,
],
    arSelectFields: ['NAME', 'PROPERTY_PRODUCT.ID', 'PROPERTY_AUTHOR']
);

$reviews = [];
while ($review = $obReviews->GetNext()) {
    if (!in_array($review['PROPERTY_AUTHOR_VALUE'], $allowedUserIds)) {
        continue;
    }
    $reviews[] = $review;
}


$productWithReviews = [];

foreach ($arResult['ITEMS'] as $key => $arItem)
{
    foreach ($reviews as $review) {
        if (!$arResult['FIRST_REVIEW']) {
            $arResult['FIRST_REVIEW'] = $review['NAME'];
        }

        if ($arItem['ID'] == $review['PROPERTY_PRODUCT_ID']) {
            $arItem['REVIEWS'][] = $review['NAME'];

            if (!in_array($arItem['ID'], $productWithReviews)) {
                $productWithReviews[] = $arItem['ID'];
            }
        }
    }

	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;
}

$arResult['PRODUCTS_WITH_REVIEWS'] = count($productWithReviews);

$this->__component->setResultCacheKeys(['PRODUCTS_WITH_REVIEWS']);
