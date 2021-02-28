<?php
/**
 * @var Pollen\Partial\PartialViewLoaderInterface $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <div class="PwaInstallPromotion-text">
        <?php if ($title = $this->get('title')) : ?>
            <h3 class="PwaInstallPromotion-title"><?php echo $title; ?></h3>
        <?php endif; ?>

        <?php if ($content = $this->get('content')) : ?>
            <div class="PwaInstallPromotion-content"><?php echo $content; ?></div>
        <?php endif; ?>
    </div>

    <div class="PwaInstallPromotion-action">
        <button class="PwaButton PwaButton--1 PwaButton--alt PwaInstallPromotion-button" class="Install">
            <?php _e('Installer', 'pollen-pwa'); ?>
        </button>
    </div>

    <a href="#PwaInstallPromotion-close" class="PwaInstallPromotion-close"></a>
</div>
