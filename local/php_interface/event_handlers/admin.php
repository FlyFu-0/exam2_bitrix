<?php

class AdminEventHandlers
{
    public static function onBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        $userGroups = \Bitrix\Main\Engine\CurrentUser::get()->getUserGroups();

        if (!in_array(CONTENT_EDITOR_GROUP_ID, $userGroups)) {
            return;
        }

        $aGlobalMenu = ['global_menu_content' => $aGlobalMenu['global_menu_content']];

        $newMenu = [];
        foreach ($aModuleMenu as $item) {
            if ($item['parent_menu'] === 'global_menu_content') {
                $newMenu[] = $item;
            }
        }
        $aModuleMenu = $newMenu;

        $aGlobalMenu['global_menu_quick'] = [
            'menu_id' => 'quick_access',
            'text' => \Bitrix\Main\Localization\Loc::getMessage('QUICK_ACCESS'),
            'title' => \Bitrix\Main\Localization\Loc::getMessage('QUICK_ACCESS'),
            'sort' => 200,
        ];

        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_quick',
            'title' => \Bitrix\Main\Localization\Loc::getMessage('LINK_1'),
            'text' => \Bitrix\Main\Localization\Loc::getMessage('LINK_1'),
            'url' => 'https://test1'
        ];
        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_quick',
            'title' => \Bitrix\Main\Localization\Loc::getMessage('LINK_2'),
            'text' => \Bitrix\Main\Localization\Loc::getMessage('LINK_2'),
            'url' => 'https://test2'
        ];
    }
}
