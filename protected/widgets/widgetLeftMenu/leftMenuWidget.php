<?
class leftMenuWidget extends CWidget
{
    /**
     * @var string объект проекта
     */
    public $project;
 
    /**
     * Запуск виджета
     */
    public function run()
    {
    
	
	$modelUser = User::model()->findByAttributes(array('id' => Yii::app()->user->id));
	$projectList = Project::model()->findAllByAttributes(array('user_id' => Yii::app()->user->id));
	$accessRight = 0;
	foreach(Yii::app()->authManager->roles as $rest) {
	    if ($modelUser->role == $rest->name) {
		  $accessRight = $rest->data;
	    }
	}
	if($accessRight) {
	    $projectList = Project::model()->findAll();
	}
	
	$arrayItems = [];
	foreach ($projectList as $modelProject) {
		if ( in_array($modelProject->host ,$arrayItems) ) {
			continue;
		}
		$arrayItems[] = $modelProject->host;
		if (!$this->project or $this->project->id != $modelProject->id) {
			echo $this->render('index', array('model' => $modelProject));
		} 
	}


    }
}

?>