<?php

class MailEventHandler
{
    public static function onSendUserInfo(&$arFields)
    {
        $userClass = \Bitrix\Main\UserTable::query()
            ->setSelect([USER_CLASS_FIELD])
            ->setFilter([
                'ACTIVE' => 'Y',
                'ID' => $arFields['USER_FIELDS']['ID']
            ])
            ->fetch();

        $userClassUnderfiend = \Bitrix\Main\Localization\Loc::getMessage('CLASS_UNDERFIEND');

        $arFields['FIELDS']['CLASS'] = $userClassUnderfiend;

        if (!$userClass[USER_CLASS_FIELD]) {
            return;
        }

        $obUserClassName = CUserFieldEnum::GetList([], [
            'ACTIVE' => 'Y',
            'ID' => $userClass[USER_CLASS_FIELD]
        ]);
        if (!$obUserClassName) {
            return;
        }

        $arUserClassName = $obUserClassName->GetNext();
        $arFields['FIELDS']['CLASS'] = $arUserClassName['VALUE'] ?? $userClassUnderfiend;
    }
}
