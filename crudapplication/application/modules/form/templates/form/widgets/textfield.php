<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' xlarge';

?>

<div class="clearfix <?=(isset($error))?'error':''?>">
    <label for="<?=$id?>"><?=$label?></label>
    <div class="input">
    	<input type="<?=$type?>" value="<?=$value?>" name="<?=$name?>" id="<?=$id?>" autocomplete="off" <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}=\"{$property_value}\" "; endforeach;?> />
        <?if (isset($error)):?>
        <span class="help-inline"><?=$error?></span> 
        <?endif;?>
        <?if (isset($description)):?>
        <span class="help-block">
            <?=$description?>
        </span>
        <?endif;?>
    </div>
</div>