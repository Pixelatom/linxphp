<p>
    <!-- we'll add a hidden field in case the checkbox is not checked, so we are able to detect it's submit -->
    <input type="hidden" name='<?=$name ?>' value='' id='' />
    <!-- actual checkbox -->
    <input type="<?=$type?>" name='<?=$name ?>' value='<?=$value ?>' id='<?=$id ?>' <? foreach ($attributes as $property_name => $property_value): echo " {$property_name}='{$property_value}' "; endforeach; ?> /><?=$label ?>
    <? if (isset($error)): ?>
        <span class="input-notification error png_bg"><?=$error ?></span> <!-- Classes for input-notification: success, error, information, attention -->
    <? endif; ?>

    <? if (isset($description)): ?>
        <br /><small><?=$description ?></small>
<? endif; ?>
</p>