<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="<?=base_url()?>assets/public/images/favicon.png">

    <!-- Stylesheets
	============================================= -->
    <link href="https://fonts.googleapis.com/css?family=Istok+Web:400,700&display=swap" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/style.min.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/swiper.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/font-icons.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/animate.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/magnific-popup.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/components/datepicker.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/components/timepicker.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/components/daterangepicker.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/et-line.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/custom.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/colors.php?color=0474c4" type="text/css" />

    <!-- Hosting Demo Specific Stylesheet -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/course/css/fonts.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/public/css/course/course.css" type="text/css" />
    <!-- / -->

    <link href="<?= base_url() ?>assets/backoffice/js/components/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

    <!-- Document Title
	============================================= -->
    <title>PPM School Of Management</title>

</head>

<body class="page-transition stretched" data-loader="2" data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800">

    <!-- Document Wrapper
	============================================= -->
    <div id="wrapper" class="clearfix">

        <!-- Top Bar
		============================================= -->
        <div id="top-bar" class="bg-color dark" style="z-index:199">
            <div class="container clearfix">

                <div class="row justify-content-between">


                    <div class="col-12 col-md-auto px-0">


                    </div>

                    <div class="col-12 col-md-auto pl-0">

                        <ul id="top-social">
                            <li><a href="#" class="si-facebook"><span class="ts-icon"><i class="icon-facebook"></i></span><span class="ts-text">Facebook</span></a></li>
                            <li><a href="#" class="si-twitter"><span class="ts-icon"><i class="icon-twitter"></i></span><span class="ts-text">Twitter</span></a></li>
                            <li><a href="#" class="si-instagram"><span class="ts-icon"><i class="icon-instagram2"></i></span><span class="ts-text">Instagram</span></a></li>
                            <li><a href="tel:+1.11.85412542" class="si-call"><span class="ts-icon"><i class="icon-call"></i></span><span class="ts-text">+1.11.85412542</span></a></li>
                            <li><a href="mailto:info@canvas.com" class="si-email3"><span class="ts-icon"><i class="icon-envelope-alt"></i></span><span class="ts-text">info@canvas.com</span></a></li>
                        </ul><!-- #top-social end -->

                    </div>
                </div>

            </div>
        </div>

        <!-- Header
		============================================= -->
        <header id="header" class="transparent-header floating-header">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row">

                        <!-- Logo
						============================================= -->
                        <div id="logo">
                            <a href="<?= base_url() ?>" class="standard-logo"><img src="<?= base_url() ?>assets/public/images/logo.png" alt="PPM School of Management"></a>
                            <a href="<?= base_url() ?>" class="retina-logo"><img src="<?= base_url() ?>assets/public/images/logo@2x.png" alt="PPM School of Management"></a>
                        </div><!-- #logo end -->

                        <div id="primary-menu-trigger">
                            <svg class="svg-trigger" viewBox="0 0 100 100">
                                <path d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20"></path>
                                <path d="m 30,50 h 40"></path>
                                <path d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20"></path>
                            </svg>
                        </div>

                        <!-- Primary Navigation
						============================================= -->
                        <nav class="primary-menu">

                            <ul class="menu-container">
                                <li class="nav-item<?= $this->uri->segment(1) == NULL || $this->uri->segment(1) == 'home' ? ' active' : '' ?>">
                                    <a class="nav-link" href="<?= base_url() ?>">Home</a>
                                </li>
                                <?php if ($this->ion_auth->logged_in()) : ?>
                                    <li class="nav-item<?= $this->uri->segment(1) == 'dashboard' ? ' active' : '' ?>">
                                        <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
                                    </li>
                                    <!-- <?php if ($admission_accepted) : ?>
                                        <li class="nav-item<?= $this->uri->segment(1) == 'dashboard' ? ' active' : '' ?>">
                                            <a class="nav-link" href="<?= base_url('payment') ?>">Pembayaran</a>
                                        </li>
                                    <?php endif; ?> -->
                                    <!-- <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Members
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="#">Dropdown Item 1</a>
                                            <a class="dropdown-item" href="#">Dropdown Item 2</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Another Dropdown Item</a>
                                        </div>
                                    </li> -->
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= base_url('home/logout') ?>">Logout</a>
                                    </li>
                                <?php else : ?>
                                    <li class="nav-item<?= $this->uri->segment(1) == 'login' ? ' active' : '' ?>">
                                        <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                                    </li>
                                    <li class="nav-item<?= $this->uri->segment(1) == 'register' ? ' active' : '' ?>">
                                        <a class="nav-link" href="<?= base_url('register') ?>">Sign Up</a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                        </nav><!-- #primary-menu end -->

                    </div>
                </div>
            </div>
        </header><!-- #header end -->

        <!-- #header end -->

        <?php $this->load->view($content) ?>



        <!-- Footer
		============================================= -->
        <footer id="footer">
            <div class="container">



            </div>
            <!-- Copyrights
			============================================= -->
            <div id="copyrights">

                <div class="container clearfix">

                    <div class="row align-items-center justify-content-between">
                        <div class="col-md-6">
                            Copyrights &copy; 2020 All Rights Reserved.<br>
                            <div class="copyright-links"><a href="#">Terms of Use</a> / <a href="#">Privacy Policy</a></div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-md-end mt-4 mt-md-0">
                            <!-- <div class="copyrights-menu copyright-links mb-0 clearfix">
                                <a href="#">Home</a>/<a href="#">About Us</a>/<a href="#">Price</a>/<a href="#">Contact</a>
                            </div> -->
                        </div>
                    </div>

                </div>

            </div><!-- #copyrights end -->

        </footer><!-- #footer end -->

    </div><!-- #wrapper end -->

    <!-- Go To Top
	============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>

    <script type="text/javascript">
        var BASE_URL = '<?= base_url() ?>';
    </script>
    <!-- External JavaScripts
	============================================= -->
    <script src="<?= base_url() ?>assets/public/js/jquery.js"></script>
    <script src="<?= base_url() ?>assets/public/js/plugins.easing.js"></script>
    <script src="<?= base_url() ?>assets/public/js/plugins.pagetransition.js"></script>
    <script src="<?= base_url() ?>assets/public/js/components/moment.js"></script>
    <script src="<?= base_url() ?>assets/public/js/components/timepicker.js"></script>
    <script src="<?= base_url() ?>assets/public/js/components/datepicker.js"></script>
    <script src="<?= base_url() ?>assets/public/js/jquery.number.min.js"></script>

    <!-- Include Date Range Picker -->
    <script src="<?= base_url() ?>assets/public/js/components/daterangepicker.js"></script>


    <script src="<?= base_url() ?>assets/public/js/components/moment/moment-with-locales.min.js"></script>
    <script type="text/javascript">
        moment.locale('id')
    </script>


    <link href="<?= base_url() ?>assets/public/css/components/select2/select2.min.css" rel="stylesheet" />
    <link href="<?= base_url() ?>assets/public/css/components/select2/select2-bootstrap.min.css" rel="stylesheet" />
    <script src="<?= base_url() ?>assets/public/js/components/select2/select2.min.js"></script>

    <script src="<?= base_url() ?>assets/backoffice/js/components/bootstrap-table/bootstrap-table.min.js"></script>

    <script src="<?= base_url() ?>assets/public/js/components/jquery-validate/jquery.validate.min.js"></script>
    <script src="<?= base_url() ?>assets/public/js/components/jquery-validate/additional-methods.min.js"></script>

    <script src="<?= base_url() ?>assets/public/js/components/bootbox.min.js"></script>

    <script src="<?= base_url() ?>assets/public/js/components/jquery-loading-overlay/loadingoverlay.min.js"></script>

    <link rel="stylesheet" href="<?= base_url() ?>assets/public/js/components/bootstrap-fileinput/css/fileinput.min.css" />
    <script src="<?= base_url() ?>assets/public/js/components/bootstrap-fileinput/js/fileinput.min.js"></script>

    <script src="<?= base_url() ?>assets/public/js/functions.js"></script>

    <script src="<?= base_url() ?>assets/public/js/custom.js"></script>

    <?php if (isset($js_plugins)) :
        foreach ($js_plugins as $d) :
    ?>
            <script src="<?= base_url(); ?>assets/public/js/<?= $d ?>"></script>
    <?php
        endforeach;
    endif;
    ?>
    <?php
    if (isset($js)) :
        foreach ($js as $d) :
    ?>
            <script src="<?= base_url(); ?>assets/public/js/<?= $d ?>?version=<?= time() ?>"></script>
    <?php
        endforeach;
    endif;
    ?>
</body>

</html>