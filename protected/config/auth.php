<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Гость',
        'bizRule' => null,
        'data' => null
    ),
    'user' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Пользователь',
        'children' => array(
            'guest',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'backend' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Доступ к панели управления',
        'children' => array(
            'user',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'moderator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Контент менеджер',
        'children' => array(
            'backend',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'administrator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Администратор',
        'children' => array(
            'moderator',
        ),
        'bizRule' => null,
        'data' => null
    ),
);

