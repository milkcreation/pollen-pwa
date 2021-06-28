<?php
/**
 * @var Pollen\Partial\PartialTemplateInterface $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <div class="PwaCameraCapture-playerArea">
        <?php echo $this->partial('tag', $this->get('player')); ?>
    </div>

    <div class="PwaCameraCapture-handler">
        <button id="takePhoto" class="PwaButton--1 PwaButton--large PwaCameraCapture-handlerButton">
            <?php echo 'Prendre une photo'; ?>
        </button>
    </div>

    <ul class="PwaCameraCapture-photos">
    </ul>
</div>
