<?php

class EmailController extends CListController {

    public $name = "Почтовые шаблоны";
    public $description = "Управление почтовыми шаблонами";
    public $type = "MailEvent";

    public function getColumns($columns = array()) {
        return parent::getColumns(array(
                    'name',
                    'code',
                    'to',
                    'from',
                    'subject',
        ));
    }

    public function getForm($element) {
        return array(
            'rows' => array(
                'Настройки',
                array('name' => array('type' => 'text', 'grid' => '6',)),
                array('code' => array('type' => 'text', 'grid' => '6')),
                array('to' => array('type' => 'text', 'grid' => '6', 'icon' => 'envelope')),
                array('from' => array('type' => 'text', 'grid' => '6', 'icon' => 'envelope')),
                'Шаблон письма',
                array('subject' => array('type' => 'text', 'grid' => '12'),),
                array(
                    'body' => array('type' => 'wysiwyg', 'grid' => '12'),
                ),
            ),
        );
    }

}

?>
