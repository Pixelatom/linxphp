<form class="form-horizontal" name="<?=$name?>" id="<?=$id?>" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"  autocomplete="off">
    <input type="hidden" name="<?=$name?>" value="submitted" />
    <fieldset> <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
    <?php foreach ($fields as $name=>$field): ?>
        <?php echo $field; ?>
    <?php endforeach ?>
    </fieldset>
</form>