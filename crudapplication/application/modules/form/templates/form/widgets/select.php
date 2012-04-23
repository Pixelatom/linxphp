<div class="control-group <?=(isset($error))?'error':''?>">
    <label class="control-label" for="<?=$id?>"><?=$label?></label>
    <div class="controls">
        <select <?foreach ($attributes as $property_name=>$property_value): echo " {$property_name}='{$property_value}' "; endforeach;?> name="<?=$name?>" id="<?=$id?>" autocomplete="off">            
            <?foreach ( $options as $value => $title ):?>
            <option value='<?=$value?>' <?=(isset($selected[$value]))?$selected[$value]:''?> ><?=$title?></option>
            <?endforeach;?>
        </select>
        <?if (isset($error)):?>
        <span class="help-inline"><?=$error?></span> 
        <?endif;?>
        <?if (isset($description)):?>
        <p class="help-block"><?=$description?></p>
        <?endif;?>
    </div>
</div>