<?php

class IblockEventHandler
{
    protected static $oldAuthor;
    public static function reviewHandler(&$arFields)
    {
        if ($arFields['IBLOCK_ID'] != REVIEWS_IBLOCK_ID) {
            return;
        }

        global $APPLICATION;

        if (mb_strlen($arFields['PREVIEW_TEXT']) < 5) {
            $APPLICATION->ThrowException(
                \Bitrix\Main\Localization\Loc::getMessage(
                    'PREVIEW_TEXT_TOO_SHORT',
                    ['#TEXT_LEN#' => mb_strlen($arFields['PREVIEW_TEXT'])]
                )
            );
            return false;
        }

        $arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $arFields['PREVIEW_TEXT']);

        $reviewIblock = \Bitrix\Iblock\Iblock::wakeUp(REVIEWS_IBLOCK_ID)->getEntityDataClass();

        $reviewAuthorId = $reviewIblock::query()
            ->setSelect(['AUTHOR_ID' => 'AUTHOR.VALUE'])
            ->setFilter([
                'ACTIVE' => 'Y',
                'ID' => $arFields['ID']
            ])
            ->fetch();

        if (!$reviewAuthorId) {
            return;
        }
        self::$oldAuthor = $reviewAuthorId['AUTHOR_ID'];
    }

    public static function onAfterIBlockElementUpdate(&$arFields)
    {
        if ($arFields['IBLOCK_ID'] != REVIEWS_IBLOCK_ID) {
            return;
        }

        $currAuthorProperty = $arFields['PROPERTY_VALUES'][REVIEW_IBLOCK_AUTHOR_PROP_ID];
        if (!$currAuthorProperty) {
            return;
        }
        $currAuthor = current($currAuthorProperty);

        if ($currAuthor['VALUE'] == self::$oldAuthor) {
            return;
        }

        CEventLog::Add(
            [
                'AUDIT_TYPE_ID' => 'ex2_590',
                'DESCRIPTION' => \Bitrix\Main\Localization\Loc::getMessage('AUTHOR_CHANGED_MESSAGE', [
                    '#ID#' => $arFields['ID'],
                    '#OLD_AUTHOR#' => htmlentities(self::$oldAuthor),
                    '#NEW_AUTHOR#' => htmlentities($currAuthor['VALUE']),
                ]),
            ]
        );
    }
}
