<?php


if (!isset($attributes['class']))
    $attributes['class'] = '';

$attributes['class'] .= ' btn';
if ($type == 'submit')
     $attributes['class'] .= ' btn-primary';

?>

<div class="form-actions">
   <button type="<?=$type?>" value="<?=$value?>" name="<?=$name?>" <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?>><?=$label?></button>
</div>

