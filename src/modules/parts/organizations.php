<?php

if(isset($_GET["orgs_count"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM organizations");
    $sql->execute();
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["orgs"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT id, name, country, region, city, address, phone, email, bio, rsakey, reputation FROM organizations LIMIT 100 OFFSET :offset");
    $sql->execute(array(":offset"=>$_GET["orgs"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["org"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count, id, name, country, region, city, address, phone, email, bio, rsakey, reputation FROM organizations WHERE id=:id");
    $sql->execute(array(":id"=>$_GET["org"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]<1){
        \LightFrame\Utils\setError(204);
    }

    echo json_encode($res);
    die();
}

if(isset($_GET["org_members"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT u.id, u.username, u.fullname, u.phone, u.email, om.role FROM organization_members AS om INNER JOIN users AS u ON (u.id=om.user) WHERE om.org=:org ORDER BY u.id");
    $sql->execute(array(":org"=>$_GET["org_members"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

?>

<span style="display: none" id="lang_name"><?php echo $lang["name"] ?></span>
<span style="display: none" id="lang_country"><?php echo $lang["country"] ?></span>
<span style="display: none" id="lang_region"><?php echo $lang["region"] ?></span>
<span style="display: none" id="lang_city"><?php echo $lang["city"] ?></span>
<span style="display: none" id="lang_address"><?php echo $lang["address"] ?></span>
<span style="display: none" id="lang_phone"><?php echo $lang["phone"] ?></span>
<span style="display: none" id="lang_email"><?php echo $lang["email"] ?></span>
<span style="display: none" id="lang_bio"><?php echo $lang["bio"] ?></span>
<span style="display: none" id="lang_rsaPublic"><?php echo $lang["rsa_public"] ?></span>
<span style="display: none" id="lang_reputation"><?php echo $lang["reputation"] ?></span>
<span style="display: none" id="lang_username"><?php echo $lang["username"] ?></span>
<span style="display: none" id="lang_fullname"><?php echo $lang["fullname"] ?></span>
<span style="display: none" id="lang_role"><?php echo $lang["role"] ?></span>
<span style="display: none" id="lang_examInvigilator"><?php echo $lang["exam_invigilator"] ?></span>
<span style="display: none" id="lang_manager"><?php echo $lang["manager"] ?></span>
<span style="display: none" id="lang_admin"><?php echo $lang["admin"] ?></span>
<span style="display: none" id="lang_close"><?php echo $lang["close"] ?></span>
<fancy-table id="orgstable" data-countlabel="<?php echo $lang["count"].": " ?>" data-count="0" data-perpage="100" data-header='["<?php echo $lang["id"] ?>", "<?php echo $lang["name"] ?>", "<?php echo $lang["country"] ?>", "<?php echo $lang["reputation"] ?>", "<?php echo $lang["operations"] ?>"]' data-order='["id", "name", "country", "reputation", "operations"]' data-content="[]" data-requestpage="ui.organizations.getOrgs"></fancy-table>
<?php if(isset($sub) && is_numeric($sub)): ?>
<script>
    ui.organizations.openDetails(<?php echo $sub ?>, true);
</script>
<?php endif ?>