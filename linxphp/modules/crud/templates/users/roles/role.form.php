<form name="<?=$name?>" id="<?=$id?>" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"  autocomplete="off">
    <input type="hidden" name="<?=$name?>" value="submitted" />
     <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
    <fieldset>
    <?php foreach ($fields as $name=>$field): ?>
        <? if ($name == 'submit') : continue; endif;?> 
        <?php echo $field; ?>        
    <?php endforeach ?>
    </fieldset>
    <fieldset>
        <table >
            <tr>
                <th>Permissions</th>                
                <th><?=$role->name?></th>                
            </tr>
            <?foreach($permissions as $permission):?>
            <tr >
                <td notranslate = "notranslate" ><?=$permission->name?></td>                
                <td><input type="checkbox" <?=($role->has_permission($permission->name))?'checked="checked"':''?> name = "permissions[]" value = "<?=$permission->name?>" /></td>               
            </tr>
            <?endforeach;?>
        </table>
    
        <div class="actions">
           <button type="submit" value="" name="submit" class=" btn primary" id="submit">Guardar</button>
        </div>
    </fieldset>

    
</form>