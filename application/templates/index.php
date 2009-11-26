<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo _('Website Translator example'); ?></title>
        <style type="text/css">
            <!--
            @charset "utf-8";
            /* CSS Document */
            body, html, div, blockquote, img, label, p, h1, h2, h3, h4, h5, h6, pre, ul,ol, li, dl, dt, dd, form, a, fieldset, input, th, td{
                margin: 0;
                padding: 0;
                border: 0;
                outline: none;
            }

            body{
                background:#eaeaea none repeat scroll 0 0;
                text-align: center;
                font-family:"Helvetica Neue",Arial,Helvetica,sans-serif;
                font-size:11.5px;
                line-height:21px;
            }

            h1, h2, h3, h4, h5, h6{
                font-size: 100%;
                margin: 0 15px;
                font-weight:normal;
                line-height:1.4em;
                padding-bottom:5px;
            }
            ul, ol{
                list-style-image: none;
                list-style-type: none;
            }
            img{
                border: 0;
            }
            h1, h2, h3 {
                font-weight:normal;
                margin:0;
                padding:0;
            }
            h1 {
                font-size:32px;
                font-weight:normal;
                line-height:1.4em;
                padding-bottom:0.7em;
                color: #3D3D3D;
                text-align: center;
                border-bottom:1px solid #EEEEEE;
            }
            h2 {
                clear:both;
                font-size:18px;
                color:#5B5A5A;
                line-height:1.4em;
                padding-bottom:5px;
            }
            ul {
                margin:0 1.5em 1.5em;
                list-style-position:inside;
                list-style-type:disc;
            }
            ol {
                list-style-image:none;
                list-style-position:inside;
                list-style-type:upper-alpha;
            }
            li {
                border-bottom:1px solid #DDDDDD;
                padding:3px 10px;
                color:#666666;
            }
            p{
                padding:0 0 1em;
            }
            hr {
                background:#DDDDDD none repeat scroll 0 0;
                border:medium none;
                clear:both;
                color:#DDDDDD;
                float:none;
                height:0.1em;
                margin:0 0 1.45em;
                width:100%;
            }
            a:link, a:visited, a:active{
                color:#3366CC;
                text-decoration:underline;
            }
            a:hover {
                color:#CC3333;
            }

            .default {
                padding-top: 5px;
                padding-right: 10px;
                padding-bottom: 5px;
                padding-left: 10px;
                margin-bottom: 5px;
                margin-top: 0px;
                margin-right: 0px;
                margin-left: 0px;
                background-color: #F2F2F2;
                border: 1px solid #CCC;
                color: #666;
            }
            .quiet {
                color:#666666;
            }
            .small {
                font-size:0.85em;
                line-height:1.875em;
                margin-bottom:1.875em;
            }
            #globalinfo{
                border-right:1px solid #EEEEEE;
                float: left;
                width: 180px;
                padding: 10px;
                font-weight: bold;
            }
            #globaldescription{
                float: right;
                width: 410px;
                padding: 10px;
            }
            #wrapper{
                width: 700px;
                margin: 20px auto;
                text-align: left;
            }
            #header{
                height: 130px;
            }
            #maincontent{
                border:1px solid #dddddd;
                background-color: #FFF;
                padding: 30px;
            }
            #header h1 {
                font-size:24px;
                margin:0 0 0 5px;
                color:#616263;
            }
            #header p{
                margin:0px 0px 10px 0px;
            }
            #header ul{
                margin: 0px;
                padding: 0px;
            }
            #header li{
                list-style: none;
                margin: 0px;
                padding:0px;
            }

            #header h1 a {
                color:#616263;
                text-decoration:none;
            }
            #header h1 a:hover {
                text-decoration: underline;
            }
            #header .content{
                float: right;
                width: 550px;
                line-height:25px;

            }
            #header img {
                float:left;
                margin-right:10px;
            }
            #header h1 span {
                display:inline;
            }
            #shadow{
                height: 13px;
                background-color: #EFEFEF;
                background-image: url(../media/layout/shadow.gif);
            }
            #footer{
                margin-top: 5px;
                color: #898989;
            }
            #tabbody{
                background-image: url(../media/layout/tabbg.png);
                background-repeat: repeat-x;
                padding: 10px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 20px;
                margin-left: 0px;
            }
            .bigtext{
                font-size: 24px;
                text-align: center;
                color: #1C4E7E;
                font-weight: bold;
            }
            .red{
                color: #FF4411;
            }
            label{
                font-size:14px;
                font-weight:bold;
            }
            input, select, textarea{
                padding: 4px 6px;
                border: solid 1px #7f9db9;
            }
            .nodisplay{
                display: none;
            }
            .grayed{
                background-color: #ebebe4;
                color: #aca899;
            }
            .left{
                float: left;
            }
            .right{
                float: right;
            }
            .spacer{
                display: block;
                clear: both;
            }
            .infoline{
                padding: 5px;
                border-bottom: solid 1px #d4e2ea;
            }

            .infoblock{
                padding: 5px;
                border: solid 1px #d4e2ea;
            }
            .ajaximageholder{
                border: solid 1px #eeede0;
                background-image: url(../media/layout/load.gif);
                background-repeat: no-repeat;
                background-position: center center;
                margin: 10px 0px;
            }
            .inputajax{
                background-image: url(../media/layout/load.gif);
                background-repeat: no-repeat;
                background-position: right center;
            }
            .inputinfo{
                font-style:italic;
                color:#171717;
                font-size: 10px;
            }

            .normal table, .disabled table, .good table, .bad table{
                border: none;
            }
            .normal table, .disabled table, .good table, .bad table{
                font-size:11px;
            }

            .normal{
                background-color: #f4f8fa;
                border: solid 1px #d4e2ea;
                margin-bottom: 5px;
            }
            .disabled{
                background-color: #f2f2f2;
                border: solid 1px #e2e2e2;
                margin-bottom: 5px;
                color: #bbb;
            }
            .good{
                background-color: #d1f39c;
                border: solid 1px #aadc63;
                margin-bottom: 5px;
            }
            .bad{
                background-color: #fcc9c9;
                border: solid 1px #dc6363;
                margin-bottom: 5px;
            }
            .ajaxholder{
                background-image: url(../media/layout/load.gif);
                background-repeat: no-repeat;
                background-position: center center;
                height: 100px;
            }
            #topbanner{
                background-color: #222222;
                height: 60px;
                text-align:center;
            }
            #topbanner .content{
                width: 700px;
                margin: 0px auto;
                position:relative;
                text-align: left;
                font-size:30px;
                color: #FFF;
            }
            #topbanner #topmenu{
                position: absolute;
                bottom: 0px;
                right: 0px;
                width: 310px;
                height: 36px;
            }
            #topmenu a:hover {
                text-decoration:underline;
            }
            #topmenu li{
                list-style: none;
                margin: 0px 0px 0px 5px;
                padding: 0px;
                float: left;
            }
            #topmenu a {
                color:#FFFFFF;
                text-decoration:none;
                font-size: 16px;
                display: block;
                padding: 2px 10px 10px 10px;
            }
            #topmenu li.active {
                background-image: url(../media/layout/arrow.gif);
                background-repeat: no-repeat;
                background-position: bottom center;
            }

            .toolblock{
                position: relative;
                width: 305px;
                height: 80px;
                background-color: #f4f8fa;
                border: solid 1px #d4e2ea;
                margin: 5px;
                float: left;
            }
            .toolblockhover{
                position: relative;
                width: 305px;
                height: 80px;
                background-color: #E4EEF3;
                border: 1px solid #CADBE6;
                margin: 5px;
                float: left;
            }
            .toolblock img, .toolblockhover img{
                position: absolute;
                left: 30px;
                top: 19px;
            }
            .toolblock h3, .toolblockhover h3{
                position: absolute;
                left: 55px;
                top: 15px;
                margin: 0px;
                padding: 0px;
                font-size: 16px;
                font-weight:bold;
            }
            .toolblock span, .toolblockhover span{
                position: absolute;
                left: 30px;
                top: 40px;
                font-size: 12px;
            }

            .new{
                position: absolute;
                top: 0px;
                right: 0px;
                background-image: url(../media/layout/new.png);
                background-repeat: no-repeat;
                width: 34px;
                height: 34px;
            }

            .code{
                padding-left: 10px;
                border-left: solid 3px #999;
                font-family:"Times New Roman", Times, serif;
                color:#333;
                font-size: 12px;
            }
            -->
        </style>
    </head><body>
        <div id="wrapper">
            <div id="maincontent">
                <h1><?php echo _('Create a multilingual website'); ?></h1>
                <hr />
                <h2><?php echo _('Change language'); ?></h2>
                <ol>
                    <li><a href="example.php?ln=en">English</a></li>
                    <li><a href="example.php?ln=nl">Nederlands</a></li>
                </ol>
                <p>
                </p>
                
                <h2><?php echo _('Paragraph'); ?> <a name="features" id="features"></a></h2>
                <p><?php echo _('This is a small paragraph with some content to show you the PHP class in action.'); ?></p>
                <ul>
                    <li><?php echo _('Translations are saved in CSV files'); ?></li>
                    <li><?php echo _('Automatic create new CSV lines when there are new strings'); ?></li>
                    <li><?php echo _('Auto translation of new strings with Google translator'); ?></li>
                    <li><?php echo _('Easy to use'); ?></li>
                </ul>
            </div>
        </div>
    </body></html>