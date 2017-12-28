<?

$parsed = parse_url( Yii::app()->getBaseUrl(true) );

$host = $parsed['host'];
$scheme = $parsed['scheme'];

$confirm_url = $scheme . '://' . $host . Yii::app()->urlManager->createUrl("main/user/email", array("id" => $user->email, "code" => $user->confirmation('email')));
$remove_url = $scheme . '://' . $host . Yii::app()->urlManager->createUrl("main/user/remove", array("id" => $user->email, "code" => $user->confirmation('remove')));

?>
<html>
    <head>
    </head>
    <body>
		<p>Вы только что зарегистрировались на проекте <a href="<?= $scheme . '://' . $host ?>" target="_blank"><?= $host ?></a></p>

		<p>
			Несмотря на то, что вы уже авторизованы и можете 
			<a href="<?= $scheme . '://' . $host . Yii::app()->urlManager->createUrl("project/index/new") ?>">добавить сайт</a>, 
			мы просим вас подтвердить ваш адрес электронной почты, для этого перейдите по ссылке <br>
			<a href="<?= $confirm_url ?>"><?= $confirm_url ?></a>
		</p>

		<p>
			Ваши данные регистрации: <br>
			ФИО: <?= $user->name ?> <br>
			Адрес электронной почты: <?= $user->email ?> <br>
		</p>

		<p>
			Если вы не регистрировались, пожалуйста перейдите по ссылке <a href="<?= $remove_url ?>">ссылке</a>
			для УДАЛЕНИЯ из нашей системы ваших данных! 
		</p>

		<p>С уважением, <br>
		администратор проекта <?= $host ?> <br>
		Лунин Дмитрий </p>
    </body>
</html>