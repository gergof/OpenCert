<form method="POST" action="">
    <fieldset class="form">
        <legend class="form__legend">
            <span><?php echo $lang["login"] ?></span>
        </legend>
        <div class="form__fields">
            <p><?php echo $lang["username"].":" ?></p>
            <input type="text" name="username" placeholder="<?php echo $lang["username"]."..." ?>" required/>
            <p><?php echo $lang["password"].":" ?></p>
            <input type="password" name="password" placeholder="<?php echo $lang["password"]."..." ?>" required/>
            <p><?php echo $lang["remember"].":" ?></p>
            <fancy-checkbox onchange="console.log('CHANGED!')"/>
        </div>
        <button type="submit" class="button"><?php echo $lang["ok"] ?></button>
    </fieldset>
</form>