<h2>Do you really want to remove? </h2>
    <p>This action can not be undone.</p>
    <form name="confirm" class="confirm-form"  method="post" action="<?=$action?>">
        <input type="hidden" name="confirm" value="true" />
   <?foreach ($ids as $id):?>
    <input type="hidden" name="list[]" value="<?=$id?>" />
    <?endforeach;?>
    
    <input  type="submit" value="Remove" class="form-submit"  />
    <a class="cancel-back" href="<?=$back?>">Cancel and back</a>
    <div class="clear"></div>
</form>