<?php

if(isset($_GET["exams_count"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM exams");
    $sql->execute();
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["exams"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT id, name, description, objectives, specifications, needed_points, timelimit, stage FROM exams ORDER BY name ASC");
    $sql->execute();
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_POST["add"])){
    if(!hasGroup(array("admin", "exam_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $data=json_decode($_POST["add"], true);

    if(!(isset($data["name"]) && isset($data["description"]) && isset($data["objectives"]) && isset($data["specifications"]) && isset($data["needed_points"]) && isset($data["timelimit"]) && isset($data["stage"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    if($data["needed_points"]<=0){
        \LightFrame\Utils\setError(111);
        die("error");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM exams WHERE name=:name");
    $sql->execute(array(":name"=>$data["name"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]>0){
        \LightFrame\Utils\setError(110);
        die("error");
    }

    $sql=$db->prepare("INSERT INTO exams (name, description, objectives, specifications, needed_points, stage) VALUES (:name, :desc, :obj, :spec, :needed_points, :stage)");
    $sql->execute(array(":name"=>$data["name"], ":desc"=>$data["description"], ":obj"=>$data["objectives"], ":spec"=>$data["specifications"], ":needed_points"=>$data["needed_points"], ":stage"=>$data["stage"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(13);
        die("ok");
    }
}

if(isset($_POST["delete"])){
    if(!hasGroup(array("admin", "exam_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("DELETE FROM exams WHERE id=:id");
    $sql->execute(array(":id"=>$_POST["delete"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(14);
        die("ok");
    }
}

if(isset($_GET["exam"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count, id, name, description, objectives, specifications, needed_points, timelimit, stage FROM exams WHERE id=:id");
    $sql->execute(array(":id"=>$_GET["exam"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]<1){
        \LightFrame\Utils\setError(204);
        die("error");
    }

    echo json_encode($res);
    die();
}

if(isset($_POST["edit"]) && isset($_POST["data"])){
    if(!hasGroup(array("admin", "exam_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $data=json_decode($_POST["data"], true);

    if(!(isset($data["name"]) && isset($data["description"]) && isset($data["objectives"]) && isset($data["specifications"]) && isset($data["needed_points"]) && isset($data["timelimit"]) && isset($data["stage"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    $sql=$db->prepare("UPDATE exams SET name=:name, description=:desc, objectives=:obj, specifications=:spec, needed_points=:needed_points, timelimit=:timelimit, stage=:stage WHERE id=:id");
    $sql->execute(array(":name"=>$data["name"], ":desc"=>$data["description"], ":obj"=>$data["objectives"], ":spec"=>$data["specifications"], ":needed_points"=>$data["needed_points"], ":timelimit"=>$data["timelimit"], ":stage"=>$data["stage"], ":id"=>$_POST["edit"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(15);
        die("ok");
    }
}

if(isset($_GET["tasks"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT id, name, description, points FROM exam_tasks WHERE exam=:id");
    $sql->execute(array(":id"=>$_GET["tasks"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["task"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count, id, name, description, points FROM exam_tasks WHERE id=:id");
    $sql->execute(array(":id"=>$_GET["task"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]<=1){
        \LightFrame\Utils\setError(204);
        die("error");
    }

    echo json_encode($res);
    die();
}

if(isset($_GET["variants"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
    }

    $sql=$db->prepare("SELECT id, instructions, file, correct, correct_file FROM exam_task_variants WHERE task=:id");
    $sql->execute(array(":id"=>$_GET["variants"]));
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_POST["new_variant"])){
    if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
        \LightFrame\Utils\setError(403);
    }

    $data=json_decode($_POST["new_variant"], true);

    if(!(isset($data["instructions"]) && isset($data["file"]) && isset($data["correct"]) && isset($data["correct_file"]) && isset($data["task"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    $sql=$db->prepare("INSERT INTO exam_task_variants (task, instructions, file, correct, correct_file) VALUES (:task, :instructions, :file, :correct, :correct_file)");
    $sql->execute(array(":task"=>$data["task"], ":instructions"=>$data["instructions"], ":file"=>$data["file"], ":correct"=>$data["correct"] ":correct_file"=>$data["correct_file"]));
    $res=$sql->rowCount();

    if($res==1){
        \LightFrame\Utils\setMessage(16);
        die("ok");
    }
    else{
        \LightFrame\Utils\setError(500);
        die("error");
    }
}

?>

<span style="display: none" id="lang_id"><?php echo $lang["id"] ?></span>
<span style="display: none" id="lang_name"><?php echo $lang["name"] ?></span>
<span style="display: none" id="lang_description"><?php echo $lang["description"] ?></span>
<span style="display: none" id="lang_objectives"><?php echo $lang["objectives"] ?></span>
<span style="display: none" id="lang_specifications"><?php echo $lang["specifications"] ?></span>
<span style="display: none" id="lang_neededPoints"><?php echo $lang["needed_points"] ?></span>
<span style="display: none" id="lang_timelimit"><?php echo $lang["timelimit"] ?></span>
<span style="display: none" id="lang_stage"><?php echo $lang["stage"] ?></span>
<span style="display: none" id="lang_draft"><?php echo $lang["draft"] ?></span>
<span style="display: none" id="lang_waitingAdmission"><?php echo $lang["waiting_admission"] ?></span>
<span style="display: none" id="lang_active"><?php echo $lang["active"] ?></span>
<span style="display: none" id="lang_retired"><?php echo $lang["retired"] ?></span>
<span style="display: none" id="lang_add"><?php echo $lang["add"] ?></span>
<span style="display: none" id="lang_save"><?php echo $lang["save"] ?></span>
<span style="display: none" id="lang_cancel"><?php echo $lang["cancel"] ?></span>
<span style="display: none" id="lang_addExamContent"><?php echo $lang["add_exam_content"] ?></span>
<span style="display: none" id="lang_delete"><?php echo $lang["delete"] ?></span>
<span style="display: none" id="lang_deleteSure"><?php echo $lang["delete_sure"] ?></span>
<span style="display: none" id="lang_close"><?php echo $lang["close"] ?></span>
<span style="display: none" id="lang_edit"><?php echo $lang["edit"] ?></span>
<span style="display: none" id="lang_openTasks"><?php echo $lang["open_tasks"] ?></span>
<span style="display: none" id="lang_tasks"><?php echo $lang["tasks"] ?></span>
<span style="display: none" id="lang_variants"><?php echo $lang["variants"] ?></span>
<span style="display: none" id="lang_points"><?php echo $lang["points"] ?></span>
<span style="display: none" id="lang_instructions"><?php echo $lang["instructions"] ?></span>
<span style="display: none" id="lang_fileAssigned"><?php echo $lang["file_assigned"] ?></span>
<span style="display: none" id="lang_fileCorrect"><?php echo $lang["file_correct"] ?></span>
<span style="display: none" id="lang_correct"><?php echo $lang["correct"] ?></span>
<span style="display: none" id="lang_newVariant"><?php echo $lang["new_variant"] ?></span>
<fancy-table id="examstable" data-countlabel="<?php echo $lang["count"].": " ?>" data-count="0" data-perpage="20" data-header='["<?php echo $lang["id"] ?>", "<?php echo $lang["name"] ?>", "<?php echo $lang["stage"] ?>", "<?php echo $lang["operations"] ?>"]' data-order='["id", "name", "stage", "operations"]' data-content="[]" data-requestpage="ui.exams.getExams"></fancy-table>
<br style="line-height: 5em"/>
<?php if(hasGroup("admin", "exam_editor")): ?>
    <button type="button" onclick="ui.exams.add()" class="button"><i class="fa fa-plus"></i> <?php echo $lang["add"] ?></button>
<?php endif ?>
<?php if(isset($sub) && is_numeric($sub)): ?>
<script>
    ui.exams.openDetails(<?php echo $sub ?>, true);
</script>
<?php endif ?>