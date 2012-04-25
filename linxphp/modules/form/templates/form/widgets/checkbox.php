<div class="control-group">
<label class="control-label" for="optionsCheckbox"><?=$label ?></label>
<div class="controls">
    <label class="checkbox">
    <!-- we'll add a hidden field in case the checkbox is not checked, so we are able to detect it's submit -->
    <input type="hidden" name='<?=$name ?>' value='' id='' />
    <!-- actual checkbox -->
    <!-- actual checkbox -->
    <input type="<?=$type?>" name='<?=$name ?>' value='<?=$value ?>' id='<?=$id ?>' <? foreach ($attributes as $property_name => $property_value): echo " {$property_name}='{$property_value}' "; endforeach; ?> />    
    &nbsp;
    </label>
    <?if (isset($error)):?>
    <span class="help-inline"><?=$error?></span> 
    <?endif;?>
    <?if (isset($description)):?>
    <p class="help-block"><?=$description?></p>
    <?endif;?>
</div>
</div>