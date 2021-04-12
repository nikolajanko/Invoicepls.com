<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Make attractive, professional invoices in a single click with the invoice generator.">
    <meta name="author" content="">

    <title>Invoicepls.com</title>
    <link rel="icon" href="/assets/invoice.png" type="image/x-icon">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- DateTimePicker CSS -->
    <link href="/assets/vendor/datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Select Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <!-- Data Table -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/datatables.min.css"/>
    <!--Custom CSS -->
    <link rel="stylesheet" href="<?= isset($css) ? $css : '' ?>">
</head>

<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark static-top" id="navbar_color">
    <div class="container">
        <div>
            <img src="/assets/invoice.png" id="navbar_image">
            <a class="navbar-brand" href="/invoice"> Invoicepls.com</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto nav-pills">
                <li class="nav-item">
                    <a class="nav-link" id="invoice" href="/invoice"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
                </li>
                <?php
                if(isset($user_data) && !empty($user_data)){ ?>
                    <li class="nav-item">
                        <a class="nav-link" id="invoices" href="/invoices"><i class="fa fa-file-text" aria-hidden="true"></i> My Invoices <span class="badge badge-secondary"><?= isset($count) && !empty($count) ? $count : '' ?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="settings" href="/settings"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a>
                    </li>
                <?php
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= isset($user_data) && !empty($user_data) ? 'd-none' : '' ?>" id="register" href="/register"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($user_data) && !empty($user_data) ? 'd-none' : '' ?>" id="login" href="/login"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a>
                </li>
                <?php
                if(isset($user_data) && !empty($user_data)){ ?>
                    <div class="dropdown">
                        <li class="btn nav-link dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?='Hi, '.$user_data['username']?>
                        </li>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="/login/log_out"><i class="fa fa-sign-out" aria-hidden="true"></i> Log out</a>
                        </div>
                    </div>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<?= isset($content) ? $content : show_404(); ?>

<!-- Bootstrap core JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DateTimePicker JavaScript -->
<script src="/assets/vendor/datetimepicker/jquery.datetimepicker.full.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- Select Picker -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<!-- Date Table -->
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/datatables.min.js"></script>
<!-- JavaScript for this page-->
<script src="<?= isset($js) ? $js : '' ?>"></script>

</body>
</html>
