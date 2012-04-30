
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?=$site_name?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <style>
            body {
                padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
            }
        </style>
        <link href="css/bootstrap-responsive.css" rel="stylesheet">        

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <?foreach($css as $link):?>
            <link href="<?=$link?>" rel="stylesheet">        
        <?endforeach;?>
    </head>

    <body>

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#"><?=$site_name?></a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            <?foreach ($navigation as $route=>$menu):?>
                            <?if(is_array($menu)):?>
                            <li class="dropdown">
                                <a href="#"
                                    class="dropdown-toggle"
                                    data-toggle="dropdown">
                                    <?=$menu[0]?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                <?foreach ($menu[1] as $route=>$menuitem):?>
                                    <li <?=(url::factory()->get_param('route')==$route)?'class="active"':''?>><a href="<?=$route?>"><?=$menuitem?></a></li>
                                <?endforeach?>
                                </ul>
                            </li>
                            <?else:?>
                            <li <?=(url::factory()->get_param('route')==$route)?'class="active"':''?>><a href="<?=$route?>"><?=$menu?></a></li>
                            <?endif;?>
                            <?endforeach?>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

        <div class="container">
            <?if (isset($breadcrumb)):?>
            <ul class="breadcrumb">
            <? $i = 0; foreach ($breadcrumb as $route=>$title): $i++; 
            if ($i == (count($breadcrumb) )) break;?>
            <li><a href="<?=url::factory()->clear_params()->set_param('route',$route)?>"><?=$title?></a> <span class="divider">/</span></li>
            <?endforeach;?>
            <li class="active"><?=$breadcrumb[$route]?></li>
            </ul>
            <?endif;?>
            
            <? if (!empty($messages)): ?>
                <? foreach ($messages as $msg): ?>
                    <div class="alert alert-<?= ($msg['type']=="warning")?"":$msg['type'] ?>">
                        <?= $msg['message'] ?>
                    </div>
                <? endforeach; ?>
            <? endif; ?>

            <?=$content?>

        </div> <!-- /container -->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster 
        
        <script src="js/bootstrap-transition.js"></script>
        <script src="js/bootstrap-alert.js"></script>
        <script src="js/bootstrap-modal.js"></script>
        
        <script src="js/bootstrap-scrollspy.js"></script>
        <script src="js/bootstrap-tab.js"></script>
        <script src="js/bootstrap-tooltip.js"></script>
        <script src="js/bootstrap-popover.js"></script>
        <script src="js/bootstrap-button.js"></script>
        <script src="js/bootstrap-collapse.js"></script>
        <script src="js/bootstrap-carousel.js"></script>
        <script src="js/bootstrap-typeahead.js"></script>
        -->
        
        <?foreach($script as $src):?>
            <script src="<?=$src?>"></script>
        <?endforeach;?>
        

    </body>
</html>
