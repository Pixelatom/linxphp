<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' text-input';

?>
<p>
        <label for='<?=$id?>'><?=$label?></label>
        <textarea <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> name='<?=$name?>' id='<?=$id?>'><?=$value?></textarea>

        <?if (isset($error)):?>
        <span class="input-notification error png_bg"><?=$error?></span> <!-- Classes for input-notification: success, error, information, attention -->
        <?endif;?>

        <?if (isset($description)):?>
        <br /><small><?=$description?></small>
        <?endif;?>
</p>