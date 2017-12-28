<?php

class CImageHelper {

    public static function thumb($fileName, $width, $height, $master = NULL) {
        Yii::import('common.extensions.image.Image');
        $pathinfo = pathinfo($fileName);
        $basepath = Yii::getPathOfAlias('root');
        if (!file_exists($basepath . $pathinfo["dirname"] . "/.tmb")) {
            mkdir($basepath . $pathinfo["dirname"] . "/.tmb", 0777, true);
        }
        $newFileName = $pathinfo["dirname"] . "/.tmb/" . $pathinfo["filename"] . "w$width" . "h$height" . "m$master." . $pathinfo["extension"];

        if (file_exists($basepath . $newFileName))
            return $newFileName;
        $image = new Image($basepath . $fileName);
        $image->resize($width, $height, $master);
        $image->save($basepath . $newFileName);
        return $newFileName;
    }

    public static function crop($fileName, $width, $height, $master = NULL, $gs = false) {
        Yii::import('common.extensions.image.Image');
        $pathinfo = pathinfo($fileName);
        $basepath = Yii::getPathOfAlias('root');
        if (!file_exists($basepath . $pathinfo["dirname"] . "/.tmb")) {
            mkdir($basepath . $pathinfo["dirname"] . "/.tmb", 0777, true);
        }
        $newFileName = $pathinfo["dirname"] . "/.tmb/" . $pathinfo["filename"] . "crop_w$width" . "h$height" . "m$master." . ($gs ? "gs" : "") . "." . $pathinfo["extension"];
        if (file_exists($basepath . $newFileName))
            return $newFileName;

        list($w, $h) = getimagesize($basepath . $fileName);
        $ratioW = $width / $w;
        $ratioH = $height / $h;
        $k = max($ratioW, $ratioH);

        $image = new Image($basepath . $fileName);
        $image->resize( $k * $w, $k * $h, $master);
        $image->crop($width, $height);
        if ($gs)
            $image->grayscale();
        $image->save($basepath . $newFileName);
        return $newFileName;
    }

}

?>
