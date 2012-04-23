<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' input-xlarge';
?>
<div class="control-group <?=(isset($error))?'error':''?>">
    <label class="control-label" for="<?=$id?>"><?=$label?></label>
    <div class="controls">
        <input type="<?=$type?>" value="<?=$value?>" name="<?=$name?>" id="<?=$id?>" autocomplete="off" <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}=\"{$property_value}\" "; endforeach;?>>
        <?if (isset($error)):?>
        <span class="help-inline"><?=$error?></span> 
        <?endif;?>
        <?if (isset($description)):?>
        <p class="help-block"><?=$description?></p>
        <?endif;?>
    </div>
</div>