<?php
if (!isset($attributes['class']))
    $attributes['class'] = '';
$attributes['class'] .= ' small-input';


?>

<p>
        <label for="<?=$id?>"><?=$label?></label>
        <select <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> name="<?=$name?>" id="<?=$id?>" autocomplete="off">
            <?=($null)?'<option value="">Please Select</option>':'';?>
	    <?foreach ( $options as $value => $title ):?>

                <option value='<?=$value?>' <?=$selected[$value]?> ><?=$title?></option>

            <?endforeach;?>
        </select>

        <?if (isset($error)):?>
        <span class="input-notification error png_bg"><?=$error?></span> <!-- Classes for input-notification: success, error, information, attention -->
        <?endif;?>

        <?if (isset($description)):?>
        <br /><small><?=$description?></small>
        <?endif;?>        
</p>