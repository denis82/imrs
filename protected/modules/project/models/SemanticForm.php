<?php

class SemanticForm extends CFormModel {
    public $text;
    public $project;

    public function rules() {
        return array(
            array('text', 'required'),
        );
    }

    public function attributeLabels() {
        return array(
            'text' => 'Список фраз через запятую или с новой строки',
        );
    }

    public function save() {
        $phrase = array();

        $lines = explode("\n", $this->text);

        foreach ($lines as $l) {
            $words = explode(',', $l);

            foreach ($words as $w) {
                $s = trim($w);

                if (strlen($s)) {
                    $phrase[] = mb_strtolower($s, 'utf-8');
                }
            }
        }

        if (count($phrase)) {
            $sql = "insert ignore into tbl_semantic (project_id, phrase, created_date) values ";

            $parameters = array(
                'pid' => $this->project->id,
            );

            foreach ($phrase as $i => $j) {
                if ($i > 0) $sql .= ', ';

                $sql.= '(:pid, :ph' . $i . ', NOW())';

                $parameters['ph' . $i] = $j;
            }

            Yii::app()->db->createCommand($sql)->execute($parameters);
        }

        return true;
    }

}


