<?php

class SearchEventHandler
{
    public static function beforeIndex($arFields)
    {
        if ($arFields['PARAM2'] != REVIEWS_IBLOCK_ID) {
            return;
        }

        $reviewIblock = \Bitrix\Iblock\Iblock::wakeUp(REVIEWS_IBLOCK_ID)->getEntityDataClass();

        $reviews = $reviewIblock::query()
            ->setSelect(['*', 'USER_CLASS_ID' => 'USER.' . USER_CLASS_FIELD])
            ->setFilter([
                'ACTIVE' => 'Y',
                'USER.ACTIVE' => 'Y',
                'ID' => $arFields['ITEM_ID']
            ])
            ->registerRuntimeField(
                null,
                new \Bitrix\Main\Entity\ReferenceField(
                    'USER',
                    \Bitrix\Main\UserTable::class,
                    \Bitrix\Main\ORM\Query\Join::on('this.AUTHOR.VALUE', 'ref.ID')
                )
            )
            ->fetch();

        if (!$reviews['USER_CLASS_ID']) {
            return;
        }

        $obUserClass = CUserFieldEnum::GetList([], [
            'ACTIVE' => 'Y',
            'ID' => $reviews['USER_CLASS_ID']
        ]);
        if (!$obUserClass) {
            return;
        }

        $arUserClass = $obUserClass->GetNext();
        if(!$arUserClass['VALUE']) {
            return;
        }

        $arFields['TITLE'] .= '. ' . $arUserClass['VALUE'];

        return $arFields;
    }
}
