<?php

if(isset($_GET["users"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die();
    }

    $sql=$db->prepare("SELECT u.id, u.username, u.fullname, u.country, u.region, u.city, u.address, u.phone, u.email, GROUP_CONCAT(gm.group SEPARATOR ', ') AS groups FROM users AS u LEFT JOIN group_members AS gm ON (gm.user=u.id) WHERE u.id<>1 GROUP BY u.id ORDER BY u.fullname ASC LIMIT 100 OFFSET :offset");
    $sql->execute(array(":offset"=>100*$_GET["users"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($res);
    die();
}

if(isset($_GET["count"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die();
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM users WHERE id<>1");
    $sql->execute();
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_POST["new_passwd"]) && isset($_POST["passwd"]) && isset($_POST["passwd_conf"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die();
    }

    if($_POST["passwd"]!=$_POST["passwd_conf"]){
        \LightFrame\Utils\setError(203);
    }
    else{
        $sql=$db->prepare("UPDATE users SET password=:passwd WHERE id=:id");
        $sql->execute(array(":passwd"=>PasswordStorage::create_hash($_POST["passwd"]), ":id"=>$_POST["new_passwd"]));
        $res=$sql->rowCount();

        if($res<0){
            \LightFrame\Utils\setError(500);
        }
        else{
            \LightFrame\Utils\setMessage(3);
        }
    }
    die();
}

if(isset($_GET["groupsfor"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die();
    }

    $sql=$db->prepare("SELECT `group` FROM group_members WHERE user=:id");
    $sql->execute(array(":id"=>$_GET["groupsfor"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    $sql=$db->prepare("SELECT id AS `group` FROM groups");
    $sql->execute();
    $all=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array("allGroups"=>$all, "memberOf"=>$res));
    die();
}

if(isset($_POST["new_groups"]) && isset($_POST["groups"])){
    if(!hasGroup("admin")){
        \LightFrame\Utils\setError(403);
        die();
    }
    
    $sql=$db->prepare("DELETE FROM group_members WHERE user=:id");
    $sql->execute(array(":id"=>$_POST["new_groups"]));

    $groups=json_decode($_POST["groups"]);

    if(count($groups)>0){
        $vals="";
        $arr=array();
        foreach($groups as $g){
            $vals.="(?, ?), ";
            array_push($arr, $_POST["new_groups"], $g);
        }
        $vals=rtrim($vals, ", ");

        $sql=$db->prepare("INSERT INTO group_members (user, `group`) VALUES ".$vals);
        $sql->execute($arr);
    }

    \LightFrame\Utils\setMessage(4);
    die();
}

?>

<span style="display: none" id="lang_newPassword"><?php echo $lang["new_password"] ?></span>
<span style="display: none" id="lang_ok"><?php echo $lang["ok"] ?></span>
<span style="display: none" id="lang_cancel"><?php echo $lang["cancel"] ?></span>
<span style="display: none" id="lang_password"><?php echo $lang["password"] ?></span>
<span style="display: none" id="lang_passwordConf"><?php echo $lang["password_conf"] ?></span>
<span style="display: none" id="lang_setMemberOf"><?php echo $lang["set_memberof"] ?></span>
<div id="userstable">
    <div class="table__holder">
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo $lang["id"] ?></th>
                    <th><?php echo $lang["username"] ?></th>
                    <th><?php echo $lang["fullname"] ?></th>
                    <th><?php echo $lang["groups"] ?></th>
                    <th><?php echo $lang["country"] ?></th>
                    <th><?php echo $lang["region"] ?></th>
                    <th><?php echo $lang["city"] ?></th>
                    <th><?php echo $lang["address"] ?></th>
                    <th><?php echo $lang["phone"] ?></th>
                    <th><?php echo $lang["email"] ?></th>
                    <th><?php echo $lang["operations"] ?></th>
                </tr>
            </thead>
            <tbody id="usertable_content">
                <!-- Data goes here -->
            </tbody>
        </table>
    </div>
    <div class="table__pageswitch">
        <span class="table__pageswitch__count">
            <?php echo $lang["count"].": " ?>
            <span id="usertable_count"></span>
        </span>
        <span class="table__pageswitch__pages" id="usertable_pages"></span>
    </div>
    <script>
        ui.users.initTable()
    </script>
</div>