<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' input_text';
if (isset($error)){
    $attributes['class'] .= ' with_error'; 
}

?>
<div class="porta_input">
        <label for="<?=$id?>"><?=$label?></label>
        <input type="<?=$type?>" name="<?=$name?>" id="<?=$id?>" autocomplete="off" <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> />
        <?if (isset($description)):?>
        <span class="input_help"><?=$description?></span>
        <?endif;?>
        <?if (isset($error)):?>
        <span class="error_field" ><?=$error?></span>
        <?endif;?>
</div>