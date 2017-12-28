<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WBlock extends CWidget {

    public $code;

    public function run() {
        $block = Block::model()->findByAttributes(array('code' => $this->code));
        if (isset($block)) {
            echo $block->content;
        }
    }

}

?>
