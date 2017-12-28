<!DOCTYPE html>
<!--[if IE 8]> <html lang="ru" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="ru" class="ie9"> <![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>Панель управления | <?= $this->title; ?></title>    
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-footer-fixed page-full-width">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="container-fluid">
                <!-- BEGIN LOGO -->
                <a class="brand" href="<?= Yii::app()->urlManager->createUrl("site/index"); ?>">
                    <?= Yii::app()->name; ?>
                </a>
                <!-- END LOGO -->
                <div class="navbar hor-menu hidden-phone hidden-tablet">
                    <div class="navbar-inner">
                        <ul class="nav">

                            <? foreach ($this->menu as $menuItem): ?>
                                <li class="<?= $menuItem["active"] ? "active" : "" ?>">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">
                                        <? if ($menuItem["active"]): ?><span class="selected"></span><? endif; ?>
                                        <i class="icon-<?= $menuItem["icon"] ?>"></i> 
                                        <?= $menuItem["label"] ?>
                                        <span class="arrow "></span>
                                    </a>                        
                                    <ul class="dropdown-menu">
                                        <? foreach ($menuItem["items"] as $menuSubItem): ?>
                                            <li class="<?= $menuSubItem["active"] ? "active" : "" ?>">                                        
                                                <a href="<?= $menuSubItem["url"] ?>">

                                                    <i class="icon-<?= $menuSubItem["icon"] ?>"></i> 

                                                    <?= $menuSubItem["label"] ?>
                                                </a>
                                            </li>
                                        <? endforeach; ?>                            
                                    </ul>
                                    <b class="caret-out"></b>
                                </li>
                            <? endforeach; ?> 
                        </ul>
                    </div>
                </div>
                <!-- BEGIN TOP NAVIGATION MENU -->              
                <ul class="nav pull-right" style="margin-top: 5px;">
                    <!-- BEGIN USER LOGIN DROPDOWN -->                        
                    <li class="dropdown user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <? $user = User::model()->findByPk(Yii::app()->user->id); ?>
                            <img alt="" src="<?= $user->smallAvatar; ?>" />
                            <span class="username"><?= $user->name; ?></span>
                            <i class="icon-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/" target="_blank"><i class="icon-arrow-right"></i> Перейти на сайт</a></li>                                
                            <li><a href="<?= Yii::app()->urlManager->createUrl("main/user/update", array("id" => Yii::app()->user->id)); ?>"><i class="icon-user"></i> Профиль</a></li>
                            <li class="divider"></li>
                            <li><a href="<?= Yii::app()->urlManager->createUrl("site/logout"); ?>"><i class="icon-key"></i> Выход</a></li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
                <!-- END TOP NAVIGATION MENU --> 
            </div>
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container row-fluid">            
        <!-- BEGIN PAGE -->
        <div class="page-content">                
            <!-- BEGIN PAGE CONTAINER-->        
            <div class="container-fluid">
                <!-- BEGIN PAGE HEADER-->
                <div class="row-fluid">
                    <div class="span12">                            
                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                        <h3 class="page-title">
                            <?= $this->title ?>                    
                            <!--<small><?= $this->description ?></small>-->
                        </h3>

                        <ul class="breadcrumb">
                            <? if (($this->id == 'site' && $this->action->id == 'index')): ?>
                                <li>
                                    <i class="icon-home"></i>
                                    <span>Рабочий стол</span>                                     
                                </li>
                            <? else: ?>
                                <li>
                                    <i class="icon-home"></i>
                                    <a href="<?= Yii::app()->urlManager->createUrl("site/index"); ?>">Рабочий стол</a> 
                                    <i class="icon-angle-right"></i>
                                </li>
                            <? endif; ?>
                            <? foreach ($this->breadcrumbs as $key => $crumb): ?>
                                <li>
                                    <? if (is_string($key)): ?>
                                        <a href="<?= $crumb ?>"><?= $key ?></a>
                                        <i class="icon-angle-right"></i>
                                    <? else: ?>
                                    <li><span><?= $crumb ?></span></li>
                                <? endif; ?>
                                </li>
                            <? endforeach; ?>                                
                        </ul>

                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <!-- END PAGE HEADER-->
                <!-- BEGIN PAGE CONTENT-->
                <?= $content; ?>
                <!-- END PAGE CONTENT-->
            </div>
            <!-- END PAGE CONTAINER-->
        </div>
        <!-- END PAGE -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="footer">            
        <div class="span pull-right">
            <span class="go-top"><i class="icon-angle-up"></i></span>
        </div>
    </div>
</body>
<!-- END BODY -->
</html>
<!--
@import url(http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,800,300,600,700&subset=latin,cyrillic);
-->