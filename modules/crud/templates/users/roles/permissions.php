
        <form action='' method="post" enctype="multipart/form-data"  autocomplete="off">
            <table class="zebra-striped" >
                <tr>
                    <th>Permissions/Roles</th>
                    <?foreach ($roles as $role):?>
                    <th><?=$role->name?></th>
                    <?endforeach;?>

                </tr>
                <?foreach($permissions as $permission):?>
                <tr >
                    <td  ><?=$permission->name?></td>
                    <?foreach ($roles as $role):?>
                    <td><input type="checkbox" <?=($role->has_permission($permission->name))?'checked="checked"':''?> name = "permissions[<?=$role->id?>][]" value = "<?=$permission->name?>" /></td>
                    <?endforeach;?>
                </tr>
                <?endforeach;?>

            </table>

            <button type="submit" value="Submit" class="btn primary">Guardar</button>
        </form>
  