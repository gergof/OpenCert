import $ from "jquery";
import marked from "marked";
import {loadMessages} from "./global.js";
import Modal from "./components/Modal.js";

export const getExams=(event) => {
    //get count
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "exams", exams_count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#examstable").attr("data-count", count.count);
    });

    //get data
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "exams", exams: event ? event.data.page : 0}
    }).then((resp) => {
        var exams=JSON.parse(resp);

        exams=exams.map((exam) => {
            var newstage="";
            switch(exam.stage){
                case 1:
                    newstage=$("#lang_waitingAdmission").text();
                    break;
                case 2:
                    newstage=$("#lang_active").text();
                    break;
                case 3:
                    newstage=$("#lang_retired").text();
                    break;
                default:
                    newstage=$("#lang_draft").text();
                    break;
            }
            return Object.assign(exam, {
                stage: newstage,
                operations: "<i class=\"fa fa-window-maximize\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.openDetails("+exam.id+")\"></i><i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.edit("+exam.id+")\"></i><i class=\"fa fa-trash\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.remove("+exam.id+")\"></i>"
            });
        });

        $("#examstable").attr("data-content", JSON.stringify(exams));
    });
};

export const remove=(id) => {
    Modal({
        title: $("#lang_deleteSure").text(),
        buttons: [
            {
                id: "delete",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_delete").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__green",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="delete"){
            $.ajax({
                url: "./modules/loader.php?load=exams",
                method: "POST",
                data: {delete: id}
            }).then(() => {
                loadMessages();
                getExams();
            });
        }
    });
};

export const getExam=(id) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "exams", exam: id}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                resolve(JSON.parse(resp));
            }
            else{
                reject();
            }
        });
    });
};

export const getTasks=(examId) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "exams", tasks: examId}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                resolve(JSON.parse(resp));
            }
            else{
                reject();
            }
        });
    });
};

export const getTask=(taskId) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "exams", task: taskId}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                resolve(JSON.parse(resp));
            }
            else{
                reject();
            }
        });
    });
};

export const getVariants=(taskId) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "exams", variants: taskId}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                resolve(JSON.parse(resp));
            }
            else{
                reject();
            }
        });
    });
};

export const getVariant=(variantId) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "exams", variant: variantId}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                resolve(JSON.parse(resp));
            }
            else{
                reject();
            }
        });
    });
};

export const openDetails=async (id, noredirect=false) => {
    var content=$("<div></div>");

    var exam=await getExam(id);
    var stage="";
    switch(exam.stage){
        case 1:
            stage=$("#lang_waitingAdmission").text();
            break;
        case 2:
            stage=$("#lang_active").text();
            break;
        case 3:
            stage=$("#lang_retired").text();
            break;
        default:
            stage=$("#lang_draft").text();
            break;
    }
    var data=$("<table style=\"border-bottom: 3em\"></table>");
    $("<tr><td><b>"+$("#lang_id").text()+":</b></td><td>"+exam.id+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_name").text()+":</b></td><td>"+exam.name+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_description").text()+":</b></td><td>"+marked(exam.description)+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_objectives").text()+":</b></td><td>"+marked(exam.objectives)+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_specifications").text()+":</b></td><td>"+marked(exam.specifications)+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_neededPoints").text()+":</b></td><td>"+exam.needed_points+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_timelimit").text()+":</b></td><td>"+exam.timelimit+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_stage").text()+":</b></td><td>"+stage+"</td></tr>").appendTo(data);
    data.appendTo(content);

    if(!noredirect){
        window.history.pushState({site: "exams/"+id}, null, "./exams/"+id);
    }

    Modal({
        title: exam.name,
        content: content.html(),
        buttons: [
            {
                id: "openTasks",
                action: () => {
                    openTasks(id);
                },
                class: "button",
                label: $("#lang_openTasks").text()
            },
            {
                id: "close",
                action: "close",
                class: "button",
                label: $("#lang_close").text()
            }
        ]
    }).then(() => {
        window.history.pushState({site: "exams"}, null, "../exams");
    });
};

export const add=() => {
    Modal({
        title: $("#lang_add").text(),
        content: $("#lang_addExamContent").text(),
        fields: [
            {
                id: "name",
                name: $("#lang_name").text(),
                type: "text"
            },
            {
                id: "description",
                name: $("#lang_description").text(),
                type: "textarea"
            },
            {
                id: "objectives",
                name: $("#lang_objectives").text(),
                type: "textarea"
            },
            {
                id: "specifications",
                name: $("#lang_specifications").text(),
                type: "textarea"
            },
            {
                id: "needed_points",
                name: $("#lang_neededPoints").text(),
                type: "number",
                min: 1
            },
            {
                id: "timelimit",
                name: $("#lang_timelimit").text(),
                type: "number",
                min: 0
            },
            {
                id: "stage",
                name: $("#lang_stage").text(),
                type: "select",
                options: [
                    {
                        id: 0,
                        name: $("#lang_draft").text()
                    },
                    {
                        id: 1,
                        name: $("#lang_waitingAdmission").text()
                    },
                    {
                        id: 2,
                        name: $("#lang_active").text()
                    },
                    {
                        id: 3,
                        name: $("#lang_retired").text()
                    }
                ]
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "check",
                label: $("#lang_save").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "./modules/loader.php?load=exams",
                method: "POST",
                data: {add: JSON.stringify(resp.formdata)}
            }).then(() => {
                loadMessages();
                getExams();
            });
        }
    });
};

export const edit=async (id) => {
    var exam=await getExam(id);

    Modal({
        title: $("#lang_edit").text(),
        content: $("#lang_addExamContent").text(),
        fields: [
            {
                id: "name",
                name: $("#lang_name").text(),
                type: "text",
                value: exam.name
            },
            {
                id: "description",
                name: $("#lang_description").text(),
                type: "textarea",
                value: exam.description
            },
            {
                id: "objectives",
                name: $("#lang_objectives").text(),
                type: "textarea",
                value: exam.objectives
            },
            {
                id: "specifications",
                name: $("#lang_specifications").text(),
                type: "textarea",
                value: exam.specifications
            },
            {
                id: "needed_points",
                name: $("#lang_neededPoints").text(),
                type: "number",
                min: 1,
                value: exam.needed_points
            },
            {
                id: "timelimit",
                name: $("#lang_timelimit").text(),
                type: "number",
                min: 0,
                value: exam.timelimit
            },
            {
                id: "stage",
                name: $("#lang_stage").text(),
                type: "select",
                options: [
                    {
                        id: 0,
                        name: $("#lang_draft").text()
                    },
                    {
                        id: 1,
                        name: $("#lang_waitingAdmission").text()
                    },
                    {
                        id: 2,
                        name: $("#lang_active").text()
                    },
                    {
                        id: 3,
                        name: $("#lang_retired").text()
                    }
                ],
                value: exam.stage
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "check",
                label: $("#lang_save").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "./modules/loader.php?load=exams",
                method: "POST",
                data: {edit: id, data: JSON.stringify(resp.formdata)}
            }).then(() => {
                loadMessages();
                getExams();
            });
        }
    });
};

export const openTasks=(examId) => {
    const updateTable=async () => {
        var tasks=await getTasks(examId);

        tasks=tasks.map((t) => {
            return Object.assign(t, {
                operations: "<i class=\"fa fa-edit task_button_edit\" style=\"margin: 0 0.3em\" data-id=\""+t.id+"\"></i><i class=\"fa fa-trash task_button_delete\" style=\"margin: 0 0.3em\" data-id=\""+t.id+"\"></i><i class=\"fa fa-tasks\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.openVariants("+t.id+")\"></i>"
            });
        });

        $("#tasks_table").attr("data-content", JSON.stringify(tasks));

        //add handlers to buttons that need a callback as well. Clear the old handlers before
        document.getElementById("tasks_table").$("#table_content").off("click");
        document.getElementById("tasks_table").$("#table_content").on("click", ".task_button_edit", function(){
            editTask($(this).data("id"), updateTable);
        });
        document.getElementById("tasks_table").$("#table_content").on("click", ".task_button_delete", function(){
            deleteTask($(this).data("id"), updateTable);
        });
    }

    var html="<fancy-table id=\"tasks_table\" data-header='[\""+$("#lang_id").text()+"\", \""+$("#lang_name").text()+"\", \""+$("#lang_description").text()+"\", \""+$("#lang_points").text()+"\", \""+$("#lang_operations").text()+"\"]' data-order='[\"id\", \"name\", \"description\", \"points\", \"operations\"]' data-content=\"[]\" data-nofooter=\"true\"></fancy-table>";

    Modal({
        title: $("#lang_tasks").text(),
        content: html,
        buttons: [
            {
                id: "newTask",
                action: () => {
                    newTask(examId, updateTable);
                },
                class: "button",
                icon: "plus",
                label: $("#lang_newTask").text()
            },
            {
                id: "ok",
                action: "close",
                class: "button",
                label: $("#lang_close").text()
            }
        ]
    });

    updateTable();
};

export const newTask=(examId, callback) => {
    Modal({
        title: $("#lang_newTask").text(),
        fields: [
            {
                id: "name",
                name: $("#lang_name").text()
            },
            {
                id: "description",
                name: $("#lang_description").text(),
                type: "textarea"
            },
            {
                id: "points",
                name: $("#lang_points").text(),
                type: "number",
                min: "1"
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "check",
                label: $("#lang_add").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {new_task: JSON.stringify(Object.assign(resp.formdata, {exam: examId}))}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};

export const editTask=async (taskId, callback) => {
    var task=await getTask(taskId);

    Modal({
        title: $("#lang_edit").text(),
        fields: [
            {
                id: "name",
                name: $("#lang_name").text(),
                value: task.name
            },
            {
                id: "description",
                name: $("#lang_description").text(),
                type: "textarea",
                value: task.description
            },
            {
                id: "points",
                name: $("#lang_points").text(),
                type: "number",
                min: "1",
                value: task.points
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "save",
                label: $("#lang_save").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {edit_task: JSON.stringify(Object.assign(resp.formdata, {id: taskId}))}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};

export const deleteTask=async (taskId, callback) => {
    Modal({
        title: $("#lang_deleteSure").text(),
        buttons: [
            {
                id: "delete",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_delete").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__green",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="delete"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {delete_task: taskId}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};

export const openVariants=(taskId) => {
    const updateTable=async () => {
        var variants=await getVariants(taskId);

        variants=variants.map((v) => {
            return Object.assign(v, {
                operations: "<i class=\"fa fa-edit variant_button_edit\" stlye=\"margin: 0 0.3em\" data-id=\""+v.id+"\"></i><i class=\"fa fa-trash variant_button_delete\" style=\"margin: 0 0.3em\" data-id=\""+v.id+"\"></i>"
            });
        });

        $("#variants_table").attr("data-content", JSON.stringify(variants));

        //remove old listeners plus add listeners
        document.getElementById("variants_table").$("#table_content").off("click");
        document.getElementById("variants_table").$("#table_content").on("click", ".variant_button_edit", function(){
            editVariant($(this).data("id"), updateTable);
        });
        document.getElementById("variants_table").$("#table_content").on("click", ".variant_button_delete", function(){
            deleteVariant($(this).data("id"), updateTable);
        });
    }


    var content=$("<div></div>");

    var table="<fancy-table id=\"variants_table\" data-header='[\""+$("#lang_id").text()+"\", \""+$("#lang_instructions").text()+"\", \""+$("#lang_fileAssigned").text()+"\", \""+$("#lang_correct").text()+"\", \""+$("#lang_fileCorrect").text()+"\", \""+$("#lang_operations").text()+"\"]' data-order='[\"id\", \"instructions\", \"file_assigned\", \"correct\", \"file_correct\", \"operations\"]' data-content=\"[]\" data-nofooter=\"true\"></fancy-table>";
    content.html(table);

    Modal({
        title: $("#lang_variants").text(),
        content: content.html(),
        buttons: [
            {
                id: "newVariant",
                action: () => {
                    newVariant(taskId, updateTable)
                },
                class: "button",
                icon: "plus",
                label: $("#lang_newVariant").text()
            },
            {
                id: "ok",
                action: "close",
                class: "button",
                label: $("#lang_close").text()
            }
        ]
    });

    updateTable();
};

export const newVariant=async (taskId, callback) => {
    var task=await getTask(taskId);

    var content=$("<div></div>");

    var taskDetails=$("<table></table>");
    $("<tr><td><b>"+$("#lang_name").text()+": </b></td><td>"+task.name+"</td></tr>").appendTo(taskDetails);
    $("<tr><td><b>"+$("#lang_description").text()+": </b></td><td>"+marked(task.description)+"</td></tr>").appendTo(taskDetails);
    $("<tr><td><b>"+$("#lang_points").text()+": </b></td><td>"+task.points+"</td></tr>").appendTo(taskDetails);
    taskDetails.appendTo(content);

    Modal({
        title: $("#lang_newVariant").text(),
        content: content.html(),
        fields: [
            {
                id: "instructions",
                name: $("#lang_instructions").text(),
                type: "textarea"
            },
            {
                id: "file",
                name: $("#lang_fileAssigned").text()
            },
            {
                id: "correct",
                name: $("#lang_correct").text(),
                type: "textarea"
            },
            {
                id: "correct_file",
                name: $("#lang_fileCorrect").text()
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "check",
                label: $("#lang_add").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {new_variant: JSON.stringify(Object.assign(resp.formdata, {task: taskId}))}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};

export const editVariant=async (variantId, callback) => {
    var variant=await getVariant(variantId);

    Modal({
        title: $("#lang_edit").text(),
        fields: [
            {
                id: "instructions",
                name: $("#lang_instructions").text(),
                type: "textarea",
                value: variant.instructions
            },
            {
                id: "file",
                name: $("#lang_fileAssigned").text(),
                value: variant.file||""
            },
            {
                id: "correct",
                name: $("#lang_correct").text(),
                type: "textarea",
                value: variant.correct
            },
            {
                id: "correct_file",
                name: $("#lang_fileCorrect").text(),
                value: variant.correct_file||""
            }
        ],
        buttons: [
            {
                id: "ok",
                action: "submit",
                class: "button button__green",
                icon: "save",
                label: $("#lang_save").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__red",
                icon: "times",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {edit_variant: JSON.stringify(Object.assign(resp.formdata, {id: variantId}))}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};

export const deleteVariant=async (variantId, callback) => {
    Modal({
        title: $("#lang_deleteSure").text(),
        buttons: [
            {
                id: "delete",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_delete").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__green",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="delete"){
            $.ajax({
                url: "../modules/loader.php?load=exams",
                method: "POST",
                data: {delete_variant: variantId}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    callback();
                }
            });
        }
    });
};