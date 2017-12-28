<?php

class CPictureBehavior extends CActiveRecordBehavior {

    public $fields;

    public function afterValidate($event) {
        foreach ($this->fields as $file => $name) {
            $uploader = CUploadedFile::getInstance($this->owner, $file);
            if (isset($uploader)) {
                $code = strtolower(get_class($this->owner));
                $sourcePath = pathinfo($uploader->getName());
                if (!file_exists(Yii::app()->basePath . '/../upload/' . $code . '/'))
                    mkdir(Yii::app()->basePath . '/../upload/' . $code . '/', 0777, true);
                $photo = strtolower('/upload/' . $code . '/' . $sourcePath['basename']);
                $i = 1;
                while (file_exists(Yii::app()->basePath . '/..' . $photo)) {
                    $pinfo = pathinfo($photo);
                    $photo = $pinfo['dirname'] . DIRECTORY_SEPARATOR . $pinfo['filename'] . '_' . $i . '.' . $pinfo['extension'];
                }
                $this->owner->{$name} = $photo;
                $uploader->saveAs(Yii::app()->basePath . '/..' . $photo);
            }
        }
        return parent::afterValidate($event);
    }

}

?>
