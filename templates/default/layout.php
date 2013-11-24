<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Buckysroom">
    <meta name="keywords" content="Buckysroom">
    <title><?php echo isset($BUCKYS_GLOBALS['title']) ? $BUCKYS_GLOBALS['title'] : "BuckysRoom"?></title>
    <?php buckys_render_stylesheet(); ?>
    <!--[if lt IE 9]>
    <script src="<?php echo DIR_WS_JS?>html5shiv.js"></script>
    <![endif]-->
    <?php buckys_render_javascripts(false); ?>
</head>
<body>
    <div id="wrapper">
        <?php require(dirname(__FILE__) . '/header.php') ?>
        <?php require(dirname(__FILE__) . '/content/' . $BUCKYS_GLOBALS['content'] . '.php') ?>        
        <?php require(dirname(__FILE__) . '/footer.php') ?>
    </div>
    <?php buckys_render_javascripts(true); ?>
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-38965481-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
</body>
</html>