<?php

class StaffController extends CListController
{

    public $name = 'Сотрудники';
    public $description = '';
    public $type = 'Staff';
    public $order = 'id asc';

    public function getColumns($columns = array())
    {
        return array(
            'name',
            'price',
            array(
                'class' => 'CAdminButtonColumn',
                'template' => '{update} {remove}',
                'buttons' => array(
                    'update' => array(
                        'label' => 'Изменить',
                        'icon' => 'edit',
                        'color' => 'purple',
                        'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/update", array("id"=>$data->id))',
                    ),
                    'remove' => array(
                        'label' => 'Удалить',
                        'icon' => 'trash',
                        'color' => 'red',
                        'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/remove", array("id"=>$data->id))',
                        'class' => 'remove-element',
                    ),
                ),
                'htmlOptions' => array('style' => 'width:350px;'),
            )
        );
    }

    public function getForm($element)
    {
        return array(
            'rows' => array(
                'Параметры сотрудника',
                array(
                    'name' => array('type' => 'text'),
                ),
                array(
                    'price' => array('type' => 'text'),
                ),
            ),
        );
    }

}
