<?php
/**
 * @var Pollen\Partial\PartialTemplate $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <?php if ($title = $this->get('title')) : ?>
        <h3 class="<?php echo $this->get('classes.title'); ?>" data-pwa-installer="title"><?php echo $title; ?></h3>
    <?php endif; ?>

    <?php if ($content = $this->get('content')) : ?>
        <div class="<?php echo $this->get('classes.content'); ?>" data-pwa-installer="content">
            <?php echo $content; ?>
        </div>
    <?php endif; ?>

    <?php if ($handler = $this->get('handler')) : ?>
    <a href="#" class="<?php echo $this->get('classes.handler'); ?>" data-pwa-installer="handler">
        <?php echo $handler; ?>
    </a>
    <?php endif; ?>

    <button class="<?php echo $this->get('classes.button'); ?>" data-pwa-installer="button">
        <?php echo $this->get('button'); ?>
    </button>

    <a href="#" class="<?php echo $this->get('classes.close'); ?>" data-pwa-installer="close">
        <?php echo $this->get('close'); ?>
    </a>
</div>
