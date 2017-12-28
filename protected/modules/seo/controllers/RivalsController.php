<?php

class RivalsController extends CListController
{

	public $name = "Конкуренты";
	public $description = "";
	public $type = "Project";
	public $order = "id asc";

	public function getColumns($columns = array())
	{
		return array(
			'name',
			array(
				'class' => 'CAdminButtonColumn',
				'template' => '{rivals} {report}',
				'buttons' => array(
					'rivals' => array(
						'label' => 'посмотреть конкурентов',
						'icon' => 'list',
						'color' => 'green',
						'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/rivals", array("id"=>$data->id))',
					),
					'report' => array(
						'label' => 'Отчет',
						'icon' => 'calendar',
						'color' => 'blue',
						'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/report", array("id"=>$data->id))',
					),
				),
				'htmlOptions' => array('style' => 'width:270px;'),
			)
		);
	}

	private function getRegions()
	{
		$content = file_get_contents(dirname(__FILE__) . "/../files/regions.txt");
		$rows = explode("\r\n", $content);
		$result = array();
		foreach ($rows as $row) {
			$r = explode("\t", $row);
			$result[$r[0]] = $r[1];
		}
		return $result;
	}

	public function actionReport($id)
	{
		Yii::import("application.modules.seo.models.*");
		$project = Project::model()->findByPk($id);

		$this->title = "Отчет по " . $project->name;
		$this->breadcrumbs["Конкуренты"] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->breadcrumbs[] = $this->title;

		//Отчет по позициям
		$date = Yii::app()->dateFormatter->formatDateTime(Yii::app()->request->getParam('date', date("Y-m-d")), 'medium', false);
		$orientation = Yii::app()->request->getParam('orientation', 'horizontal');
		$regions = array();
		foreach ($project->regions as $regionId) {
			$regions[$regionId] = Region::getByPk($regionId);
		}
		$region = Yii::app()->request->getParam('region', current($project->regions));
		$all = Yii::app()->request->getParam('all', 0);
		//Конкуренты
		$keywords = explode(",", $project->keywords);
		$keywordId = Yii::app()->request->getParam('keyword', 0);

		$sort = Yii::app()->request->getParam('sort', "position");

		foreach ($keywords as $keyword) {
			if (Yii::app()->request->getParam('rebuild', 0)) {
				$criteria = new CDbCriteria();
				$criteria->condition = "project_id=:project_id and keyword=:keyword and region_id=:region and date=:date";
				$criteria->params = array("project_id" => $project->id, "keyword" => $keyword, "region" => $region, "date" => date("Y-m-d", strtotime($date)));
			}
			$this->buildGrid($project, $region, $keyword, $date, $all);

			$criteria = new CDbCriteria();
			$criteria->condition = "project_id=:project_id and keyword=:keyword and region_id=:region and date=:date";
			$criteria->params = array("project_id" => $project->id, "keyword" => $keyword, "region" => $region, "date" => date("Y-m-d", strtotime($date)));
			$criteria->order = $sort;
			$rows = ReportKeywords::model()->findAll($criteria);
			$grids[$keyword] = $rows;
		}

		$this->render('report', array(
			'project' => $project,
			'date' => $date,
			'regions' => $regions,
			'region' => $region,
			'keyword' => $keywordId,
			'grids' => $grids,
			'all' => $all,
			'orientation' => $orientation,
			'sort' => $sort
		));
	}

	public function getActions()
	{
		return array();
	}

	public function calcMediana($code, $rows)
	{
		$needMediana = ReportKeywords::model()->attributeNeedMediana();
		if (in_array($code, $needMediana) || array_key_exists($code, $needMediana)) {
			$set = array();
			foreach ($rows as $row) {
				$code = $needMediana[$code] ? $needMediana[$code] : $code;
				if (!is_null($row->{$code})) {
					$set[] = $row->{$code};
				}
			}
			return CUtils::getMedian($set);
		}
		return false;
	}

	public function calcDivergence($code, $rows, $project)
	{
		$mediana = $this->calcMediana($code, $rows);
		if (is_bool($mediana))
			return false;

		foreach ($rows as $row) {
			//echo $row->domain_id . ":" . $project->domain_id . ":" . $project->name . "<br/>";
			if ($row->domain_id == $project->domain_id) {
				return $row->{$code} - $mediana;
			}
		}
		return false;
	}

	private function buildGrid($project, $region, $keyword, $date, $all = false)
	{
		$date = date("Y-m-d", strtotime($date));

		$criteria = new CDbCriteria();
		$criteria->condition = "keyword=:keyword and date=:date and project_id=:project_id";
		$criteria->params = array("keyword" => $keyword, "date" => $date, "project_id" => $project->id);
		if (ReportKeywords::model()->find($criteria) > 0)
			return;


		$criteria = new CDbCriteria();
		$criteria->condition = "keyword=:keyword and checkdate=:date and region_id=:region and position<=9";
		$criteria->params = array("keyword" => $keyword, "date" => $date, "region" => $region);
		$criteria->order = "position";
		$criteria->limit = 10;
		$rivals = YPositions::model()->findAll($criteria);
		if (count($rivals) == 0)
			return array();
		//Список доменов		
		$exist = false;
		foreach ($rivals as $yp) {
			if ($yp->domain_id == $project->id)
				$exist = true;
			$report = new ReportKeywords();
			$report->project_id = $project->id;
			$report->date = $date;
			$report->position = $yp->position;
			$report->keyword = $yp->keyword;
			$report->region_id = $yp->region_id;
			$report->domain_id = $yp->domain_id;
			$report->snp_title = $yp->title;
			$report->snp_text = $yp->passage;

			$whois = Whois::check($yp->domain_id);
			if (isset($whois)) {
				$report->age = ceil((time() - strtotime($whois->created)) / 24 / 3600);
			}
			if($solomono = Solomono::check($yp->domain_id, $date)){
				$report->ankors = $solomono->anchors;
				$report->hin = $solomono->hin;
				$report->hout = $solomono->hout;
				// $report->index = $solomono->index_count;
				$report->top50 = $solomono->top50;
				$report->donors = $solomono->din;
			}
			$yandex = Yandex::check($yp->domain_id);
			if ($yandex) {
				$report->tic = $yandex->tic;
				$report->pr = $yandex->pr;
				$report->yac = $yandex->yac;
				$report->index = $yandex->index;
			}
			$ct = CitationTrust::check($yp->domain_id, $date);
			if ($ct) {
				$report->citation = $ct->citation;
				$report->trust = $ct->trust;
			}
			if (!$report->save()) {
				var_dump($report->errors);
			}
		}
		if (!$exist) {
			$criteria = new CDbCriteria();
			$criteria->condition = "keyword=:keyword and checkdate=:date and region_id=:region and domain_id=:domain_id";
			$criteria->params = array("keyword" => $keyword, "date" => $date, "region" => $region, "domain_id" => $project->domain_id);
			$yp = YPositions::model()->find($criteria);
			$report = new ReportKeywords();
			$report->project_id = $project->id;
			$report->date = $date;
			$report->keyword = $keyword;
			$report->region_id = $region;
			$report->domain_id = $project->domain_id;
			if ($yp) {
				$report->position = $yp->position;
				$report->snp_title = $yp->title;
				$report->snp_text = $yp->passage;
			} else {
				$report->position = 9999;
				$report->snp_title = "";
				$report->snp_text = "";
			}
			$whois = Whois::check($project->domain_id);
			if (isset($whois)) {
				$report->age = ceil((time() - strtotime($whois->created)) / 24 / 3600);
			}
			$solomono = Solomono::check($project->domain_id, $date);
			if ($solomono) {
				$report->ankors = $solomono->anchors;
				$report->hin = $solomono->hin;
				$report->hout = $solomono->hout;
				// $report->index = $solomono->index_count;
				$report->top50 = $solomono->top50;
				$report->donors = $solomono->din;
			}
			$yandex = Yandex::check($project->domain_id);
			if ($yandex) {
				$report->tic = $yandex->tic;
				$report->pr = $yandex->pr;
				$report->yac = $yandex->yac;
				$report->index = $yandex->index;
			}
			$ct = CitationTrust::check($project->domain_id, $date);
			if ($ct) {
				$report->citation = $ct->citation;
				$report->trust = $ct->trust;
			}

			if (!$report->save()) {
				var_dump($report->errors);
			}
		}
	}

	public function actionRivals($id)
	{
		Yii::import("application.modules.seo.models.*");
		$project = Project::model()->findByPk($id);

		$this->title = "Список конкурентов " . $project->name;
		$this->breadcrumbs["Конкуренты"] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->breadcrumbs[] = $this->title;

		$orientation = Yii::app()->request->getParam('orientation', 'vertical');
		$regions = array();
		foreach ($project->regions as $regionId) {
			$regions[$regionId] = Region::getByPk($regionId);
		}
		$region = Yii::app()->request->getParam('region', current($project->regions));
		$date = Yii::app()->dateFormatter->formatDateTime(Yii::app()->request->getParam('date', date("Y-m-d")), 'medium', false);
		$all = Yii::app()->request->getParam('all', 0);
		$sort = Yii::app()->request->getParam('sort', "position");

		$this->buildGrid2($project, $date, $region);

		$criteria = new CDbCriteria();
		$criteria->condition = "date=:date and region_id=:region_id and project_id=:project_id";
		$criteria->params = array("date" => date('Y-m-d', strtotime($date)), "region_id" => $region, "project_id" => $project->id);
		$criteria->order = $sort . ' desc';

		$this->render("rivals", array(
			"dataProvider" => new CActiveDataProvider('ReportRivals', array('criteria' => $criteria, 'pagination' => array('pageSize' => 30))),
			"project" => $project,
			"date" => $date,
			"regions" => $regions,
			"region" => $region,
			"orientation" => $orientation,
			"rows" => ReportRivals::model()->findAll($criteria),
			'all' => $all,
			'sort' => $sort
		));
	}

	private function buildGrid2($project, $date, $region)
	{
		$date = date('Y-m-d', strtotime($date));
		$criteria = new CDbCriteria();
		$criteria->condition = "date=:date and region_id=:region_id and project_id=:project_id";
		$criteria->params = array("date" => $date, "region_id" => $region, "project_id" => $project->id);
		if (ReportRivals::model()->find($criteria) > 0)
			return;

		//Параметры отбора
		$keywords = explode(",", $project->keywords);
		$incondition = array();
		foreach ($keywords as $k)
			$incondition[] = '"' . $k . '"';

		//Выборка из базы
		$subquery = "select count(st.keyword) from " . YPositions::model()->tableName() . " as st where st.domain_id=t.domain_id and st.region_id=" . $region . " and st.position<=10 and st.checkdate='" . date("Y-m-d", strtotime($date)) . "' and st.keyword in (" . implode(",", $incondition) . ")";
		$criteria = new CDbCriteria();
		$criteria->select = "t.*, ($subquery) as countkeys";
		$criteria->condition = "t.position<=10 and t.checkdate='" . $date . "' and t.keyword in (" . implode(",", $incondition) . ")";
		$criteria->group = "domain_id";
		$criteria->order = "countkeys desc";

		$rivals = YPositions::model()->findAll($criteria);
		//Список доменов
		$exist = false;
		$cnt = 0;
		foreach ($rivals as $yp) {
			if ($yp->domain_id == $project->id)
				$exist = true;
			$report = new ReportRivals();
			$report->project_id = $project->id;
			$report->date = $date;
			$report->position = $yp->countkeys;
			$report->domain_id = $yp->domain_id;
			$report->region_id = $yp->region_id;

			$whois = Whois::check($yp->domain_id);
			if (isset($whois)) {
				$report->age = ceil((time() - strtotime($whois->created)) / 24 / 3600);
			}
			$solomono = Solomono::check($yp->domain_id, $date);
			if ($solomono) {
				$report->ankors = $solomono->anchors;
				$report->hin = $solomono->hin;
				$report->hout = $solomono->hout;
				$report->top50 = $solomono->top50;
				$report->donors = $solomono->din;
			}
			$yandex = Yandex::check($yp->domain_id);
			if ($yandex) {
				$report->tic = $yandex->tic;
				$report->pr = $yandex->pr;
				$report->yac = $yandex->yac;
				$report->index = $yandex->index;
			}
			$ct = CitationTrust::check($yp->domain_id, $date);
			if ($ct) {
				$report->citation = $ct->citation;
				$report->trust = $ct->trust;
			}
			if ($cnt < 20 || $exist)
				if (!$report->save()) {
					var_dump($report->errors);
				}
			if ($cnt >= 20 && $exist)
				break;
			$cnt++;
		}
		if (!$exist) {
			$report = new ReportRivals();
			$report->project_id = $project->id;
			$report->date = $date;
			$report->region_id = $region;
			$report->domain_id = $project->domain_id;
			$report->position = 0;
			$whois = Whois::check($project->domain_id);
			if (isset($whois)) {
				$report->age = ceil((time() - strtotime($whois->created)) / 24 / 3600);
			}
			$solomono = Solomono::check($project->domain_id, $date);
			if ($solomono) {
				$report->ankors = $solomono->anchors;
				$report->hin = $solomono->hin;
				$report->hout = $solomono->hout;
				$report->top50 = $solomono->top50;
				$report->donors = $solomono->din;
			}
			$yandex = Yandex::check($project->domain_id);
			if ($yandex) {
				$report->tic = $yandex->tic;
				$report->pr = $yandex->pr;
				$report->yac = $yandex->yac;
				$report->index = $yandex->index;
			}
			$ct = CitationTrust::check($project->domain_id, $date);
			if ($ct) {
				$report->citation = $ct->citation;
				$report->trust = $ct->trust;
			}

			if (!$report->save()) {
				var_dump($report->errors);
			}
		}
	}

	public function actionDomain($id, $domain_id, $region_id)
	{

		Yii::import("application.modules.seo.models.*");
		$project = Project::model()->findByPk($id);

		//Параметры отбора
		$keywords = explode(",", $project->keywords);
		$incondition = array();
		foreach ($keywords as $k) {
			$incondition[] = '"' . $k . '"';
		}
		$regions = array();
		foreach ($project->regions as $regionId) {
			$regions[$regionId] = Region::getByPk($regionId);
		}
		$date = Yii::app()->dateFormatter->formatDateTime(Yii::app()->request->getParam('date', date("Y-m-d")), 'medium', false);

		//Настройка интерфейса
		$this->title = "Список ключевых фраз по домену " . $domain . " в регионе: " . $regions[$region_id];
		$this->breadcrumbs["Конкуренты"] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
		$this->breadcrumbs["Список конкурентов " . $project->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/rivals', array("id" => $id, "region_id" => $region_id, "checkdate" => $date));
		$this->breadcrumbs[] = $this->title;

		//Выборка из базы
		$criteria = new CDbCriteria();
		$criteria->select = "t.*";
		$criteria->condition = "t.domain_id=:domain_id and region_id=:region_id and t.position<=10 and t.checkdate='" . date("Y-m-d", strtotime($date)) . "' and t.keyword in (" . implode(",", $incondition) . ")";
		$criteria->params = array("domain_id" => $domain_id, "region_id" => $region_id);
		$criteria->order = "position";

		$this->render("domain", array("project" => $project, "regions" => $regions, "region" => $region_id, "rows" => new CActiveDataProvider("YPositions", array("criteria" => $criteria, 'pagination' => array('pageSize' => 1000)))));
	}

}