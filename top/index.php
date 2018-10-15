<html>
<head>

	<style>

		body { font-family: sans-serif; font-size: 14px; color: #333; line-height: 22px; }

		input[type=text] { font-family: sans-serif; font-size: 14px; color: #333; line-height: 22px; width: 500px; padding: 0 10px; }
		textarea { font-family: sans-serif; font-size: 14px; color: #333; line-height: 22px; width: 500px; padding: 5px 10px; height: 300px; }
		input[type=file] { font-family: sans-serif; font-size: 14px; color: #333; line-height: 22px; width: 500px; padding: 0 10px; margin-bottom: 10px; display: block; border: 1px solid rgba(0,0,0,.1); }

		.field { display: block; margin: 10px 0px; }
		.field label { width: 200px; font-weight: bold; display: inline-block; vertical-align: top; }

		.col { display: inline-block; width: 40%; vertical-align: top; padding: 0 20px; }

	</style>

</head>

<body>
	<form action="upload.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="10240000">
		<input type="hidden" name="form_id" value="upload">

		<div class="col">

			<div class="field">
				<label>Сайт клиента</label>
				<input type="text" name="site">
			</div>

			<div class="field">
				<label>Сайты конкурентов</label>
				<textarea name="sites"></textarea>
			</div>

		</div>

		<div class="col">

			<div class="field">
				<label>Файлы</label>

				<input type="file" name="file1">
				<input type="file" name="file2">
				<input type="file" name="file3">
				<input type="file" name="file4">
				<input type="file" name="file5">
				<input type="file" name="file6">
				<input type="file" name="file7">
				<input type="file" name="file8">
				<input type="file" name="file9">
				<input type="file" name="file10">

			</div>
		</div>

		<div class="col">

			<button type="submit">Отправить</button>

		</div>

	</form>
</body>
</html>

