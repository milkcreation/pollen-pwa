<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php echo strtolower(get_bloginfo('charset')); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php _e('Application Hors-Ligne', 'pollen-pwa'); ?></title>
    <link rel="stylesheet" href="offline.css" type="text/css" media="all">
</head>

<body class="PwaOffline">
    <div class="PwaOffline-container">
        <h1 class="PwaOffline-title"><?php _e('Hors-Ligne', 'pollen-pwa'); ?></h1>

        <div class="PwaOffline-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 612 612">
                <path d="M494.7,229.5c-17.851-86.7-94.351-153-188.7-153c-38.25,0-73.95,10.2-102,30.6l38.25,38.25c17.85-12.75,40.8-17.85,63.75-17.85c76.5,0,140.25,63.75,140.25,140.25v12.75h38.25c43.35,0,76.5,33.15,76.5,76.5c0,28.05-15.3,53.55-40.8,66.3l38.25,38.25C591.6,438.6,612,400.35,612,357C612,290.7,558.45,234.6,494.7,229.5z M76.5,109.65l71.4,68.85C66.3,183.6,0,249.9,0,331.5c0,84.15,68.85,153,153,153h298.35l51,51l33.15-33.15L109.65,76.5L76.5,109.65z M196.35,229.5l204,204H153c-56.1,0-102-45.9-102-102c0-56.1,45.9-102,102-102H196.35z"/>
            </svg>
        </div>

        <p><?php _e('Cliquer sur le boutton pour recharger la page', 'pollen-pwa'); ?></p>
        <button class="PwaOffline-button PwaOffline-button--reload" type="button">
            <?php _e('Recharger', 'pollen-pwa'); ?>
        </button>
    </div>
    <script type="text/javascript" src="offline.js"></script>
</body>
</html>