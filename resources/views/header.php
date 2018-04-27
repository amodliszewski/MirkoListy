<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="MirkoListy" />
        <meta name="author" content="@IrvinTalvanen" />
        <title>#mirkolisty</title>
<?php
    $assetsVersion = 3;
?>
        <link href="/css/bootstrap.min.css?v=<?php echo $assetsVersion; ?>" rel="stylesheet" />
        <link href="/css/modern-business.css?v=<?php echo $assetsVersion; ?>" rel="stylesheet" />
        <link href="/font-awesome/css/font-awesome.min.css?v=<?php echo $assetsVersion; ?>" rel="stylesheet" type="text/css" />
<?php
    if ($app->make('App\Services\TemplateService')->getStyleId() === 1) {
?>
        <link rel="stylesheet" href="/css/black.css?v=<?php echo $assetsVersion; ?>" type="text/css" id="blackCSS" />
<?php
    }
?>

        <script type="text/javascript" src="/js/jquery.js?v=<?php echo $assetsVersion; ?>"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js?v=<?php echo $assetsVersion; ?>"></script>
        <script type="text/javascript" src="/js/jquery.cookie.js?v=<?php echo $assetsVersion; ?>"></script>
        <script type="text/javascript" src="/js/jquery.countdown.min.js?v=<?php echo $assetsVersion; ?>"></script>
        <script type="text/javascript" src="/js/notify.min.js?v=<?php echo $assetsVersion; ?>"></script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('img.avatar, img.img-polaroid').on('error', function() {
                    this.src = 'https://wykop.pl/cdn/c3397992/avatar_def.png';
                })
            });
        </script>
    </head>
    <body>
        <div class="theme-changer change-style">
            <div>&nbsp;</div>
        </div>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Menu</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">#mirkolisty</a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
<?php
    $visibleLinks = 0;

    if (!session()->has('wykopNick')) {
        $visibleLinks++;
?>
                        <li>
                            <a href="#" onClick="alert('Musisz się najpierw zalogować!'); return false;">Wołaj</a>
                        </li>
<?php
    } else {
        $visibleLinks++;
?>
                        <li>
                            <div style="color: green; padding-top: 15px; padding-bottom: 15px; padding-right: 15px; font-weight: bold;">
                                Witaj <span style="color: <?php echo $app->make('App\Services\TemplateService')->getGroupColor(session()->get('wykopGroup')); ?>"><?php echo session()->get('wykopNick'); ?></span>
                            </div>
                        </li>
<?php
        if ($app->make('WykoCommon\Services\UserService')->canAdd()) {
            $visibleLinks += 2;
?>
                        <li>
                            <a href="<?php echo route('callFormUrl'); ?>">Wołaj</a>
                        </li>
                        <li>
                            <a href="<?php echo route('addSpamlistUrl'); ?>">Dodaj nową</a>
                        </li>
<?php
        }

        $userSpamlists = $app->make('App\Services\SpamlistService')->getUserCreatedSpamlists();
        if (is_array($userSpamlists) && !empty($userSpamlists)) {
            $visibleLinks++;
?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Utworzone listy</a>
                            <ul class="dropdown-menu">
<?php
            foreach ($userSpamlists as $foreachItem) {
?>
                                <li>
                                    <a href="<?php echo route('getSpamlistUrl', array('uid' => $foreachItem['uid'])); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($foreachItem['name']); ?></a>
                                </li>
<?php
            }
?>
                            </ul>
                        </li>
<?php
        }
?>
<?php
        $userSpamlists = $app->make('App\Services\SpamlistService')->getUserJoinedSpamlists();
        if (is_array($userSpamlists) && !empty($userSpamlists)) {
            $visibleLinks++;
?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Twoje listy</a>
                            <ul class="dropdown-menu">
<?php
            foreach ($userSpamlists as $foreachItem) {
?>
                                <li>
                                    <a href="<?php echo route('getSpamlistUrl', array('uid' => $foreachItem['uid'])); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($foreachItem['name']); ?></a>
                                </li>
<?php
            }
?>
                            </ul>
                        </li>
<?php
        }

        $currentUser = $app->make('WykoCommon\Services\UserService')->getCurrentUser();
        if ($currentUser !== null && $currentUser->rights >= 20) {
            $visibleLinks += 1;
?>
                        <li>
                            <a href="<?php echo route('getScheduledItemsUrl'); ?>">Zaplanowane</a>
                        </li>
<?php
        }

        if ($currentUser !== null && $currentUser->rights == 99) {
            $visibleLinks += 1;
?>
                        <li>
                            <a href="<?php echo route('searchProfileUrl'); ?>">Profile</a>
                        </li>
<?php
        }
    }
?>
                        <li>
                            <a href="<?php echo route('getSpamlistsUrl'); ?>">Katalog</a>
                        </li>
                        <li>
                            <a href="<?php echo route('faqUrl'); ?>">FAQ</a>
                        </li>
                        <li>
                            <a href="https://wykoevent.pl">WykoEvent</a>
                        </li>
<?php
    $visibleLinks += 3;

    if ($visibleLinks < 9) {
?>
                        <li>
                            <a href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>dodatki/pokaz/747/">Notatkowator</a>
                        </li>
<?php
    }

    if (session()->has('wykopNick')) {
?>
                        <li>
                            <a href="<?php echo route('logout'); ?>">Wyloguj</a>
                        </li>
<?php
    } else {
?>
                        <li>
                            <a href="<?php echo $app->make('WykoCommon\Services\WykopService')->getLoginUrl(true); ?>">Zaloguj</a>
                        </li>
<?php
    }
?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div style="width: 100%; margin-top: 15px; margin-bottom: 5px; text-align: center">
            </div>
<?php
    if (session()->has('flashError')) {
?>
            <div class="alert alert-danger" role="alert" style="margin-top: 25px"><?php echo session()->get('flashError'); ?></div>
<?php
    }

    if (session()->has('flashSuccess')) {
?>
            <div class="alert alert-success" role="alert" style="margin-top: 25px"><?php echo session()->get('flashSuccess'); ?></div>
<?php
    }

    if (session()->has('flashInfo')) {
?>
            <div class="alert alert-info" role="alert" style="margin-top: 25px"><?php echo session()->get('flashInfo'); ?></div>
<?php
    }