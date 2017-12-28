<? 
    $this->clientScript->registerCssFile($this->assetsUrl . '/limitless_1.5/css/extras/animate.min.css'); 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/login.js'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $this->title ?></title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="login-container login-cover">

    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                <!-- Content area -->
                <div class="content pb-20">

                    <?= $content; ?>

                </div>
                <!-- /content area -->

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->

</body>
</html>
