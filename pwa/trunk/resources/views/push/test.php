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
    <title><?php _e('Test de notifications Push', 'pollen-pwa'); ?></title>
    <link rel="stylesheet" href="push-test.css" type="text/css" media="all">
</head>

<body class="PwaPushTest">
    <div class="PwaPushTest-container">
        <h1 class="PwaPushTest-title"><?php _e('Test de <br>Notifications Push', 'pollen-pwa'); ?></h1>

        <div class="PwaPushTest-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="m411 262.862v-47.862c0-69.822-46.411-129.001-110-148.33v-21.67c0-24.813-20.187-45-45-45s-45 20.187-45 45v21.67c-63.59 19.329-110 78.507-110 148.33v47.862c0 61.332-23.378 119.488-65.827 163.756-4.16 4.338-5.329 10.739-2.971 16.267s7.788 9.115 13.798 9.115h136.509c6.968 34.192 37.272 60 73.491 60 36.22 0 66.522-25.808 73.491-60h136.509c6.01 0 11.439-3.587 13.797-9.115s1.189-11.929-2.97-16.267c-42.449-44.268-65.827-102.425-65.827-163.756zm-170-217.862c0-8.271 6.729-15 15-15s15 6.729 15 15v15.728c-4.937-.476-9.94-.728-15-.728s-10.063.252-15 .728zm15 437c-19.555 0-36.228-12.541-42.42-30h84.84c-6.192 17.459-22.865 30-42.42 30zm-177.67-60c34.161-45.792 52.67-101.208 52.67-159.138v-47.862c0-68.925 56.075-125 125-125s125 56.075 125 125v47.862c0 57.93 18.509 113.346 52.671 159.138z"/>
                <path d="m451 215c0 8.284 6.716 15 15 15s15-6.716 15-15c0-60.1-23.404-116.603-65.901-159.1-5.857-5.857-15.355-5.858-21.213 0s-5.858 15.355 0 21.213c36.831 36.831 57.114 85.8 57.114 137.887z"/>
                <path d="m46 230c8.284 0 15-6.716 15-15 0-52.086 20.284-101.055 57.114-137.886 5.858-5.858 5.858-15.355 0-21.213-5.857-5.858-15.355-5.858-21.213 0-42.497 42.497-65.901 98.999-65.901 159.099 0 8.284 6.716 15 15 15z"/>
            </svg>
        </div>

        <button id="PwaPushTest-button--subscribe" class="PwaPushTest-button" type="button">
            <?php echo $this->get('PushTest.l10n.button_default'); ?>
        </button>
        <button id="PwaPushTest-button--send" class="PwaPushTest-button" type="button">
            <?php echo $this->get('PushTest.l10n.sending'); ?>
        </button>
    </div>
    <script type="text/javascript">
        <?php echo 'let PushTest = ' . json_encode($this->get('PushTest', [])) . ';'; ?>
    </script>
    <script type="text/javascript" src="push-test.js"></script>
</body>
</html>