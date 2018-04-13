<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width"/>

    <!-- For development, pass document through inliner -->

    <style type="text/css">

* {
  margin: 0;
  padding: 0;
  font-size: 100%;
  font-family: 'Avenir Next', "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
  line-height: 1.65; }

img {
  max-width: 100%;
  margin: 0 auto;
  display: block; }

body,
.body-wrap {
  width: 100% !important;
  height: 100%;
  background: #efefef;
  -webkit-font-smoothing: antialiased;
  -webkit-text-size-adjust: none; }

a {
  color: #71bc37;
  text-decoration: none; }

.text-center {
  text-align: center; }

.text-right {
  text-align: right; }

.text-left {
  text-align: left; }

.button {
  display: inline-block;
  color: white;
  background: #71bc37;
  border: solid #71bc37;
  border-width: 10px 20px 8px;
  font-weight: bold;
  border-radius: 4px; }




h3 {
  font-size: 24px; }

h4 {
  font-size: 20px; }

h5 {
  font-size: 16px; }

p, ul, ol {
  font-size: 16px;
  font-weight: normal;
  margin-bottom: 20px; }

.container {
  display: block !important;
  clear: both !important;
  margin: 0 auto !important;
  max-width: 580px !important; }
  .container table {
    width: 100% !important;
    border-collapse: collapse; }
  .container .masthead {
    padding: 40px 0;
    background: #D80C16;
    color: white; }
    .container .masthead h1 {
      margin: 0 auto !important;
      max-width: 90%;
      text-transform: uppercase; }
  .container .content {
    background: white;
    padding: 30px 35px; }
    .container .content.footer {
      background: none; }
      .container .content.footer p {
        margin-bottom: 0;
        color: #888;
        text-align: center;
        font-size: 14px; }
      .container .content.footer a {
        color: #888;
        text-decoration: none;
        font-weight: bold; }

    </style>
</head>
<body>
<table  style="width: 100% !important;height: 100%;background: #efefef;-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; background: #e8e9e8;">
    <tr>
        <td class="container">

            <!-- Message start -->
            <table style="width:100%;">
                <tr>
                    <td align="center" class="masthead" style="padding: 40px 0;background: #D80C16;color: white; ">

                        <h1 align="center" style = "font-size: 32px;  margin-bottom: 20px; line-height: 1.25;">Аудитка - незабудка!</h1>

                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <h2 align="center" style = "font-size: 28px;   margin-bottom: 20px; line-height: 1.25;">Привет дорогой друг,</h2>
                        <div style="padding-left: 40px">
                        <p>Проект <a href='<?php echo $modelProject->host; ?>'><?php echo $modelProject->name; ?></a>.</p>

                        <p>При обращении к страницам обнаружены изменения.</p>
                        
			<ul>
				<?php foreach ($arrayPages as $key => $page) {?>
					<li><a href="<?php echo $page;?>"><?php echo $key; ?></a></li>
				<?php }?>
			</ul>
			
			<p> Не доступные страницы.</p>
			<?php if (!empty($badStatusPages)) {?>
				<?php foreach ($badStatusPages as $key => $page) {?>
					<li><?php echo $key . '  =>  статус: '. $page?> </li>
				<?php }?>
                        <?php }?>
                        
                        <p>
				Что же там изменилось? А  
				<a href="<?= Yii::app()->urlManager->BaseUrl . 'project/index/download/' . $diffTextDownload . '.txt';?>">
					вот тебе ;)
				</a>
			</p>
                        <p>
				ВНИМАНИЕ: Если хочешь изменить настройку отправки писем, тебе 
				<a href="<?= Yii::app()->urlManager->BaseUrl . 'project/index/errors/' . $modelProject->id;?>">
					сюда
				</a>
			</p>
			
                        
                        <p align="right" style="padding-right: 40px" >Я и не знал, сколько на свете кретинов, пока не заглянул в Интернет.</p>
                        <p align="right" style="padding-right: 40px" ><em>– Станислав Лем</em></p>
                       </div> 
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="container">

            <!-- Message start -->
            <table align="center" >
                <tr>
                    <td class="content footer" align="center">
                        <p align="center" >Отправлено <a href="#">Аудит «СЕО Эксперт»</a></p>
                        <p align="center"><a mailto:office@seo-experts.com?subject=Сообщение с сайта seo-experts.com>office@seo-experts.com</a> | Веб-сайт: <a href="http://seo-experts.com/">seo-experts.com</a></p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>