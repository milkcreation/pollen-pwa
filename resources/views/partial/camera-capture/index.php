<?php
/**
 * @var Pollen\Partial\PartialViewTemplateInterface $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <div class="PwaCameraCapture-playerArea">
        <?php echo partial('tag', $this->get('player')); ?>
    </div>

    <div class="PwaCameraCapture-handler">
        <button id="takePhoto" class="PwaButton--1 PwaButton--large PwaCameraCapture-handlerButton">
            <?php _e('Prendre une photo', 'tify'); ?>
        </button>
    </div>

    <ul class="PwaCameraCapture-photos">
    </ul>
</div>
