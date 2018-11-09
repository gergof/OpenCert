<form method="POST" action="">
    <fieldset class="form">
        <legend class="form__legend">
            <span><?php echo $lang["login"] ?></span>
        </legend>
        <?php if($lm->isRememberingUser()): ?>
            <?php
            $sql=$db->prepare("SELECT fullname FROM users WHERE id=:id");
            $sql->execute(array(":id" => $lm->isRememberingUser()));
            $res=$sql->fetch(PDO::FETCH_ASSOC);
            ?>
            <div>
                <p><?php echo str_replace("{{fullname}}", $res["fullname"], $lang["welcomeback"]) ?></p>
                <button type="button" class="button" onclick="window.location='./?autologin'"><?php echo $lang["login"] ?></button>
                <button type="button" class="button" onclick="window.location='./?forgetuser'"><?php echo $lang["forget"] ?></button>
            </div>
        <?php else: ?>
            <div class="form__fields">
                <p><?php echo $lang["username"].":" ?></p>
                <input type="text" name="username" placeholder="<?php echo $lang["username"]."..." ?>" required/>
                <p><?php echo $lang["password"].":" ?></p>
                <input type="password" name="password" placeholder="<?php echo $lang["password"]."..." ?>" required/>
                <p><?php echo $lang["remember"].":" ?></p>
                <input id="remember_checkbox" type="checkbox" name="remember" hidden/>
                <fancy-checkbox onchange="ui.login.toggleRemember()"/>
            </div>
            <button type="submit" class="button"><?php echo $lang["ok"] ?></button>
        <?php endif ?>
    </fieldset>
</form>