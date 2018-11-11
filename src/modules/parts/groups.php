<?php

if(isset($_GET["groups"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT id, description FROM groups ORDER BY id ASC LIMIT 20 OFFSET :offset");
    $sql->execute(array(":offset"=>20*$_GET["groups"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["count"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM groups");
    $sql->execute();
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["usersfor"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT gm.user AS id, u.username, u.fullname, u.email FROM group_members AS gm INNER JOIN users AS u ON (u.id=gm.user) WHERE gm.group=:group ORDER BY u.fullname ASC");
    $sql->execute(array(":group" => $_GET["usersfor"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["group"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count, id, description FROM groups WHERE id=:gid");
    $sql->execute(array(":gid"=>$_GET["group"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]!=1){
        \LightFrame\Utils\setError(204);
        die("error");
    }

    echo json_encode($res);
    die();
}

if(isset($_POST["edit"]) && isset($_POST["description"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("UPDATE groups SET description=:desc WHERE id=:group");
    $sql->execute(array(":desc"=>$_POST["description"], ":group"=>$_POST["edit"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(5);
    }

    die("ok");
}

if(isset($_POST["removefrom"]) && isset($_POST["user"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("DELETE FROM group_members WHERE `group`=:group and user=:user");
    $sql->execute(array(":group"=>$_POST["removefrom"], ":user"=>$_POST["user"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(6);
    }

    die("ok");
}

?>

<span style="display: none" id="lang_editGroup"><?php echo $lang["edit_group"] ?></span>
<span style="display: none" id="lang_groupMembers"><?php echo $lang["group_members"] ?></span>
<span style="display: none" id="lang_description"><?php echo $lang["description"] ?></span>
<span style="display: none" id="lang_ok"><?php echo $lang["ok"] ?></span>
<span style="display: none" id="lang_cancel"><?php echo $lang["cancel"] ?></span>
<span style="display: none" id="lang_id"><?php echo $lang["id"] ?></span>
<span style="display: none" id="lang_username"><?php echo $lang["username"] ?></span>
<span style="display: none" id="lang_fullname"><?php echo $lang["fullname"] ?></span>
<span style="display: none" id="lang_email"><?php echo $lang["email"] ?></span>
<span style="display: none" id="lang_operations"><?php echo $lang["operations"] ?></span>
<span style="display: none" id="lang_close"><?php echo $lang["close"] ?></span>
<span style="display: none" id="lang_deleteSure"><?php echo $lang["delete_sure"] ?></span>
<span style="display: none" id="lang_delete"><?php echo $lang["delete"] ?></span>
<div id="grouptable">
    <div class="table__holder">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo $lang["id"] ?></th>
                    <th><?php echo $lang["description"] ?></th>
                    <th><?php echo $lang["operations"] ?></th>
                </tr>
            </thead>
            <tbody id="grouptable_content">
                <!-- content goes here -->
            </tbody>
        </table>
    </div>
    <div class="table__pageswitch">
        <span class="table__pageswitch__count">
            <?php echo $lang["count"].": " ?>
            <span id="grouptable_count"></span>
        </span>
        <span class="table__pageswitch__pages" id="grouptable_pages"></span>
    </div>
    <script>
        ui.groups.initTable();
    </script>
</div>