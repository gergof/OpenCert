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
                operations: "<i class=\"fa fa-window-maximize\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.openDetails("+exam.id+")\"></i><i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.edit("+exam.id+")\"></i><i class=\"fa fa-trash\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.remove("+exam.id+")\"></i><i class=\"fa fa-tasks\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.openTasks("+exam.id+")\"></i>"
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

export const openTasks=(id) => {
    
};