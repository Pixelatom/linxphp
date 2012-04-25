
<!DOCTYPE html> 
<html lang="en"> 
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="description" content="">
        <meta name="author" content="">
    </head>

    <body>

        <? if (!empty($messages)): ?>
        <? foreach ($messages as $msg): ?>
        <div class="alert-message <?=$msg['type'] ?>">
            <p><?=$msg['message'] ?></p>
        </div>
        <? endforeach; ?>
        <? endif; ?>
        <form action="<?=Url::factory()->clear_params()?>" method="POST" class="pull-right">
            <input name="email" class="input-small" type="text" placeholder="Username">
            <input name="password" class="input-small" type="password" placeholder="Password">
            <button class="btn" type="submit">Sign in</button>
        </form>




    </body>
</html> 