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

    </body>
</html>
