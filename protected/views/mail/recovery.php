<html>
    <head>
    </head>
    <body>

		<p>Добрый день, <?= $user->name ?></p>

		<p>Вы запросили восстановление пароля для логина <?= $user->username ?>, пожалуйста перейдите по этой ссылке чтобы ввести новый пароль к вашему логину:<br>
		<a href="<?= $link ?>"><?= $link ?></a>
		</p>

		<p>Если вы не запрашивали восстановление пароля - просто проигнорируйте и удалите это письмо.</p>

    </body>
</html>