<span style="display: none" id="lang_id"><?php echo $lang["id"] ?></span>
<span style="display: none" id="lang_name"><?php echo $lang["name"] ?></span>
<span style="display: none" id="lang_description"><?php echo $lang["description"] ?></span>
<span style="display: none" id="lang_objectives"><?php echo $lang["objectives"] ?></span>
<span style="display: none" id="lang_specifications"><?php echo $lang["specifications"] ?></span>
<span style="display: none" id="lang_neededPoints"><?php echo $lang["needed_points"] ?></span>
<span style="display: none" id="lang_stage"><?php echo $lang["stage"] ?></span>
<span style="display: none" id="lang_draft"><?php echo $lang["draft"] ?></span>
<span style="display: none" id="lang_waitingAdmission"><?php echo $lang["waiting_admission"] ?></span>
<span style="display: none" id="lang_active"><?php echo $lang["active"] ?></span>
<span style="display: none" id="lang_retired"><?php echo $lang["retired"] ?></span>
<fancy-table id="examstable" data-countlabel="<?php echo $lang["count"].": " ?>" data-count="0" data-perpage="20" data-header='["<?php echo $lang["id"] ?>", "<?php echo $lang["name"] ?>", "<?php echo $lang["stage"] ?>", "<?php echo $lang["operations"] ?>"]' data-order='["id", "name", "stage", "operations"]' data-content="[]" data-requestpage="ui.exams.getExams"></fancy-table>