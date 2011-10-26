<div class="clearfix  <?=(isset($error))?'error':''?>">
    <label for="<?=$id?>"><?=$label?></label>
    <div class="input">
        <select <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> name="<?=$name?>" id="<?=$id?>" autocomplete="off">
            <?=($null)?'<option value="">Please Select</option>':'';?>
            <?foreach ( $options as $value => $title ):?>
            <option value='<?=$value?>' <?=$selected[$value]?> ><?=$title?></option>
            <?endforeach;?>
        </select>
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
