import $ from "jquery";
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
            return Object.assign(exam, {
                operations: "<i class=\"fa fa-window-maximize\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.openDetails("+exam.id+")\"></i><i class=\"fa fa-trash\" style=\"margin: 0 0.3em\" onclick=\"ui.exams.delete("+exam.id+")\"></i>"
            });
        });

        $("#examstable").attr("data-content", JSON.stringify(exams));
    });
};

export const delete=(id) => {
    $.ajax({
        url: "./modules/loader.php?load=exams",
        method: "POST",
        data: {delete: id}
    }).then(() => {
        loadMessages();
        getExams();
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

export const openDetails=async (id) => {
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
    $("<tr><td>"+$("#lang_id").text()+"</td><td>"+exam.id+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_name").text()+"</td><td>"+exam.name+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_description").text()+"</td><td>"+exam.description+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_objectives").text()+"</td><td>"+exam.objectives+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_specifications").text()+"</td><td>"+exam.specifications+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_neededPoints").text()+"</td><td>"+exam.needed_points+"</td></tr>").appendTo(data);
    $("<tr><td>"+$("#lang_stage").text()+"</td><td>"+stage+"</td></tr>").appendTo(data);
    data.appendTo(content);

    
};