<?php

class TestCommand extends CConsoleCommand {

    public function actionTop50() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        foreach (History::model()->findAll() as $history) {
            echo $history->host . ":";
            $history->top50 = SeoUtils::getTop50($history->host);
            $history->save();
            echo " " . $history->top50;
            echo "\n";
        }
    }

    public function actionSnippet() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");

        $query = "http://xmlsearch.yandex.ru/xmlsearch?user=xsite&key=03.37624:68dcfd904d9cac84ac2e25bf79b104af&query=" . urlencode("oao ростелеком");
        $response = file_get_contents($query);
        file_put_contents(Yii::app()->basePath . "/../snippet.xml", $response);
        /*
          $dom = new DOMDocument();
          $dom->loadXML($response);
         * 
         */
    }

    public function actionTrastFlow() {

        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        foreach (History::model()->findAll() as $history) {
            echo $history->host . ":";
            list($citation, $trust) = SeoUtils::getCitationTrustFlow($history->host);
            $history->citation = $citation;
            $history->trust = $trust;
            $history->save();
            echo " " . $history->citation . " " . $history->trust;
            echo "\n";
        }
    }

    public function actionDomain() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        foreach (History::model()->findAll() as $history) {
            $domain = Domain::model()->findByAttributes(array("domain" => SeoUtils::normalizeDomain($history->host, false)));
            if (!isset($domain)) {
                $domain = new Domain;
                $domain->domain = SeoUtils::normalizeDomain($history->host, false);
                $domain->ru_domain = $convert->decode((string) $domain->domain);
                echo $domain->domain . "\n";
                $domain->save();
            } else {
                $convert = new idna_convert();
                $domain->ru_domain = $convert->decode((string) $domain->domain);
                $domain->save();
            }
        }
        $criteria = new CDbCriteria();
        $criteria->select = "domain";
        $criteria->distinct = true;
        foreach (Query::model()->findAll($criteria) as $history) {
            $domain = Domain::model()->findByAttributes(array("domain" => SeoUtils::normalizeDomain($history->domain, false)));
            if (!isset($domain)) {
                $domain = new Domain;
                $domain->domain = SeoUtils::normalizeDomain($history->domain, false);
                $domain->ru_domain = $convert->decode((string) $domain->domain);
                echo $domain->domain . "\n";
                $domain->save();
            } else {
                $convert = new idna_convert();
                $domain->ru_domain = $convert->decode((string) $domain->domain);
                $domain->save();
            }
        }
    }

    public function actionWhois() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");

        foreach (Domain::model()->findAll() as $domain) {
            $whois = Whois::model()->findByAttributes(array("domain_id" => $domain->id, "checkdate" => date("Y-m-d")));
            if (!isset($whois)) {
                echo $domain->domain . "\n";
                $whois = new Whois();
                $whois->domain_id = $domain->id;
                $whois->check();
            }
        }
    }

    public function actionYandex() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");

        //foreach (Domain::model()->findAll() as $domain) {
        $domain = Domain::model()->findByAttributes(array("domain" => "stroykamen.ru"));
        $yandex = Yandex::model()->findByAttributes(array("domain_id" => $domain->id, "checkdate" => date("Y-m-d")));
        if (!isset($yandex)) {
            $yandex = new Yandex();
            $yandex->domain_id = $domain->id;
            $yandex->check();
        }
        //}
    }

    public function actionSolomono() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");

        foreach (Domain::model()->findAll() as $domain) {
            $solomono = Solomono::model()->findByAttributes(array("domain_id" => $domain->id, "checkdate" => date("Y-m-d")));
            if (!isset($solomono)) {
                echo $domain->domain . "\n";
                $solomono = new Solomono();
                $solomono->domain_id = $domain->id;
                $solomono->check();
            }
        }
    }

    public function actionQueryConvert() {
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");

        $cirteria = new CDbCriteria();
        $cirteria->condition = "domain_id<=0";
        $cirteria->limit = 10000;

        foreach (Query::model()->findAll($cirteria) as $query) {
            $domain = Domain::model()->findByAttributes(array("domain" => SeoUtils::normalizeDomain($query->domain, false)));
            echo $domain->domain . "\n";
            $query->domain_id = $domain->id;
            $query->title = $query->getTitleSnippet();
            $query->description = $query->getPassage();
            if (!$query->save()) {
                var_dump($query->errors);
                die();
            }
        }
    }

}