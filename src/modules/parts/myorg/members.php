<?php
$sql=$db->prepare("SELECT COUNT(om.id) AS count, om.role AS role, o.id, o.name, o.country, o.region, o.city, o.address, o.phone, o.email, o.bio, o.rsakey, o.reputation FROM organization_members AS om INNER JOIN organizations AS o ON (o.id=om.org) WHERE om.user=:uid");
$sql->execute(array(":uid"=>$_SESSION["id"]));
$org=$sql->fetch(PDO::FETCH_ASSOC);

if($org["count"]!=1){
    \LightFrame\Utils\setError(113);
    die("<script>window.location='./'</script>");
}

?>

<div style="text-align: center">
    <?php if($org["role"]==2): ?><button type="button" class="button" onclick="ui.main.route('myorg/members')"><?php echo $lang["members"] ?></button><?php endif ?>
    <?php if($org["role"]>=1): ?><button type="button" class="button" onclick="ui.main.route('myorg/examinations')"><?php echo $lang["examinations"] ?></button><?php endif ?>
    <?php if($org["role"]>=1): ?><button type="button" class="button" onclick="ui.main.route('myorg/results')"><?php echo $lang["results"] ?></button><?php endif ?>
    <?php if($org["role"]==2): ?><button type="button" class="button" onclick="ui.main.route('myorg/certificates')"><?php echo $lang["certificates"] ?></button><?php endif ?>
    <button type="button" class="button" onclick="ui.main.route('myorg/statistics')"><?php echo $lang["statistics"] ?></button>
    <button type="button" class="button" onclick="ui.main.route('myorg/doexamination')"><?php echo $lang["doexamination"] ?></button>
    <br/>
    <br style="line-height: 5em"/>
</div>