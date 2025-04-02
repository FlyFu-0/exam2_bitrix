<?php

class UserEventHandler
{
    public static $authorClass = [];

    public static function onBeforeUserUpdate(&$arFields)
    {
        $user = \Bitrix\Main\UserTable::query()
            ->setSelect([USER_CLASS_FIELD])
            ->setFilter([
                'ACTIVE' => 'Y',
                'ID' => $arFields['ID']
            ])
            ->fetch();

        self::$authorClass = [
            'ID' => 0,
            'NAME' => \Bitrix\Main\Localization\Loc::getMessage(
                'CLASS_UNDERFIEND'
            ),
        ];

        if (!$user) {
            return;
        }

        if (!current($user)) {
            return;
        }

        self::$authorClass['ID'] = current($user);

        $obClassName = CUserFieldEnum::GetList([], [
            'ACTIVE' => 'Y',
            'ID' => current($user)
        ]);
        if (!$obClassName) {
            return;
        }

        $arClassName = $obClassName->GetNext();
        if (!$arClassName['VALUE']) {
            return;
        }
        self::$authorClass['NAME'] = $arClassName['VALUE'];
    }

    public static function onAfterUserUpdate(&$arFields)
    {
        if ($arFields[USER_CLASS_FIELD] == self::$authorClass['ID']) {
            return;
        }

        $newClassName= \Bitrix\Main\Localization\Loc::getMessage(
            'CLASS_UNDERFIEND'
        );

        if ($arFields[USER_CLASS_FIELD]) {
            $obClassName = CUserFieldEnum::GetList([], [
                    'ACTIVE' => 'Y',
                    'ID' => $arFields[USER_CLASS_FIELD]
            ]);
            if (!$obClassName) {
                return;
            }

            $arClassName = $obClassName->GetNext();
            if (!$arClassName['VALUE']) {
                return;
            }
            $newClassName = $arClassName['VALUE'];
        }

        \Bitrix\Main\Mail\Event::send([
            "EVENT_NAME" => "EX2_AUTHOR_INFO",
            "LID" => "s1",
            "C_FIELDS" => [
                'OLD_USER_CLASS' => self::$authorClass['NAME'],
                'NEW_USER_CLASS' => $newClassName,
            ],
        ]);
    }
}
