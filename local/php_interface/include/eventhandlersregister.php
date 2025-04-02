<?php
require __DIR__ . '/../event_handlers/iblock.php';
require __DIR__ . '/../event_handlers/user.php';
require __DIR__ . '/../event_handlers/mail.php';
require __DIR__ . '/../event_handlers/search.php';
require __DIR__ . '/../event_handlers/admin.php';

$event = \Bitrix\Main\EventManager::getInstance();

$event->addEventHandler(
    'iblock',
    'OnBeforeIBlockElementAdd',
    [IblockEventHandler::class, 'reviewHandler']
);

$event->addEventHandler(
    'iblock',
    'OnBeforeIBlockElementUpdate',
    [IblockEventHandler::class, 'reviewHandler']
);

$event->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    [IblockEventHandler::class, 'onAfterIBlockElementUpdate']
);

$event->addEventHandler(
    'main',
    'OnBeforeUserUpdate',
    [UserEventHandler::class, 'onBeforeUserUpdate']
);

$event->addEventHandler(
    'main',
    'OnAfterUserUpdate',
    [UserEventHandler::class, 'onAfterUserUpdate']
);

$event->addEventHandler(
    'main',
    'OnSendUserInfo',
    [MailEventHandler::class, 'onSendUserInfo']
);

$event->addEventHandler(
    'search',
    'BeforeIndex',
    [SearchEventHandler::class, 'beforeIndex']
);

$event->addEventHandler(
    'main',
    'OnBuildGlobalMenu',
    [AdminEventHandlers::class, 'onBuildGlobalMenu']
);