<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$metaTag = $APPLICATION->GetProperty('ex2_meta');

$APPLICATION->SetPageProperty(
    'ex2_meta',
    str_replace('#count#', $arResult['PRODUCTS_WITH_REVIEWS'], $metaTag)
);
