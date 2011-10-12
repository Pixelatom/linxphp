<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' text-input small-input';

?>
<p>
        <label for="<?=$id?>"><?=$label?></label>
        <input type="<?=$type?>" value='<?=$value?>' name="<?=$name?>" id="<?=$id?>" autocomplete="off" <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> />

        <?if (isset($error)):?>
        <span class="input-notification error png_bg"><?=$error?></span> <!-- Classes for input-notification: success, error, information, attention -->        
        <?endif;?>

        <?if (isset($description)):?>
        <br /><small><?=$description?></small>
        <?endif;?>        
</p>