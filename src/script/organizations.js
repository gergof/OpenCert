import $ from "jquery";
import {loadMessages} from "./global.js";
import Modal from "./components/Modal.js";

export const getOrgs=(event) => {
    //get count
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "organizations", orgs_count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#orgstable").attr("data-count", count.count);
    });

    //get organizations
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "organizations", orgs: event ? event.data.page : 0}
    }).then((resp) => {
        var orgs=JSON.parse(resp);

        orgs=orgs.map((org) => {
            return Object.assign(org, {
                reputation: "<reputation-slider title=\""+org.reputation+"\" data-value=\""+org.reputation+"\"></reputation-slider>",
                operations: "<i class=\"fa fa-window-maximize\" style=\"margin: 0 0.3em\" onclick=\"ui.organizations.openDetails("+org.id+")\"></i><i class=\"fa fa-star-half-alt\" style=\"margin: 0 0.3em\" onclick=\"ui.organizations.changeReputation("+org.id+")\"></i>"
            });
        });

        $("#orgstable").attr("data-content", JSON.stringify(orgs));
    });
};

export const getOrg=(id) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "organizations", org: id}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                var org=JSON.parse(resp);

                resolve(org);
            }
            else{
                reject();
            }
        });
    });
};

export const getOrgMembers=(id) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "organizations", org_members: id}
        }).then((resp) => {
            loadMessages();
            if(resp!="error"){
                var members=JSON.parse(resp);

                resolve(members);
            }
            else{
                reject();
            }
        });
    });
};

export const openDetails=async (id, noredirect=false) => {
    var content=$("<div></div>");

    var org=await getOrg(id);
    var data=$("<table style=\"border-bottom: 3em\"></table>");
    $("<tr><td><b>"+$("#lang_name").text()+": </b></td><td>"+org.name+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_country").text()+": </b></td><td>"+org.country+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_region").text()+": </b></td><td>"+org.region+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_city").text()+": </b></td><td>"+org.city+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_address").text()+": </b></td><td>"+org.address+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_phone").text()+": </b></td><td>"+org.phone+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_email").text()+": </b></td><td>"+org.email+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_bio").text()+": </b></td><td>"+org.bio+"</td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_reputation").text()+": </b></td><td><reputation-slider title=\""+org.reputation+"\" data-value=\""+org.reputation+"\"></reputation-slider></td></tr>").appendTo(data);
    $("<tr><td><b>"+$("#lang_rsaPublic").text()+": </b></td><td>"+org.rsakey.replace(/\n/g, "<br/>")+"</td></tr>").appendTo(data);
    data.appendTo(content);

    var members=await getOrgMembers(id);
    members=members.map((member) => {
        switch(member.role){
            case 1:
                member.role=$("#lang_manager").text();
                break;
            case 2:
                member.role=$("#lang_admin").text();
                break;
            default:
                member.role=$("#lang_examInvigilator").text();
                break;
        }
        return member;
    });
    var membersTable=$("<fancy-table id=\"modal_table\" data-header='[\""+$("#lang_id").text()+"\", \""+$("#lang_username").text()+"\", \""+$("#lang_fullname").text()+"\", \""+$("#lang_phone").text()+"\", \""+$("#lang_email").text()+"\", \""+$("#lang_role").text()+"\"]' data-order='[\"id\", \"username\", \"fullname\", \"phone\", \"email\", \"role\"]' data-content=\"[]\" data-nofooter=\"true\"></fancy-table>");
    membersTable.attr("data-content", JSON.stringify(members));
    membersTable.appendTo(content);

    if(!noredirect){
        window.history.pushState({site: "organizations/"+id}, null, "./organizations/"+id);
    }

    Modal({
        title: org.name,
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
        window.history.pushState({site: "organizations"}, null, "../organizations");
    });
};

export const changeReputation=async (id) => {
    var org=await getOrg(id);

    Modal({
        title: $("#lang_changeReputation").text(),
        content: $("#lang_changeReputationTooltip").text(),
        fields: [
            {
                id: "reputation",
                name: $("#lang_reputation").text(),
                type: "range",
                min: 0,
                max: 100,
                value: org.reputation
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
                url: "./modules/loader.php?load=organizations",
                method: "POST",
                data: {set_reputation: id, reputation: resp.formdata.reputation}
            }).then(() => {
                loadMessages();
                getOrgs();
            });
        }
    });
};