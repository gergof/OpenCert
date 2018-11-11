import $ from "jquery";
import Modal from "./components/Modal.js";
import {loadMessages} from "./global.js";

export const initTable=() => {
    getPageCount();
    loadGroups();
};

export const getPageCount=() => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#grouptable_count").html(count.count);

        var pages=Math.ceil(count.count/20);

        $("#grouptable_pages").html("");
        for(var i=1; i<=pages; i++){
            $("<span onclick=\"ui.groups.loadGroups("+(i-1).toString()+")\">"+i.toString()+"</span>").hide().appendTo("#grouptable_pages").fadeIn();
        }
    });
};

export const loadGroups=(page=0) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", groups: page}
    }).then((resp) => {
        var groups=JSON.parse(resp);

        var html="";
        groups.forEach((group) => {
            html+="<tr><td>"+group.id+"</td><td>"+group.description+"</td><td><i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.editGroup('"+group.id+"')\"/><i class=\"fa fa-users\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.getUsersForGroup('"+group.id+"')\"/></td></tr>";
        });
        
        $("#grouptable_content").html(html);
    });
};

export const editGroup=(group) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", group}
    }).then((resp) => {
        if(resp=="error"){
            loadMessages();
            return;
        }

        var groupdata=JSON.parse(resp);

        Modal({
            title: $("#lang_editGroup").text(),
            fields: [
                {
                    id: "description",
                    name: $("#lang_description").text(),
                    value: groupdata.description
                }
            ],
            buttons: [
                {
                    id: "ok",
                    action: "submit",
                    class: "button button__green",
                    icon: "check",
                    label: $("#lang_ok").text()
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
                    url: "./modules/loader.php?load=groups",
                    method: "POST",
                    data: {edit: group, description: resp.formdata.description}
                }).then((resp) => {
                    loadMessages();
                    initTable();
                });
            }
        });
    });
};

export const getUsersForGroup=(group) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", usersfor: group}
    }).then((resp) => {
        var users=JSON.parse(resp);

        var html="<div class=\"table__holder\"><table class=\"table\"><thead><tr><th>"+$("#lang_id").text()+"</th><th>"+$("#lang_username").text()+"</th><th>"+$("#lang_fullname").text()+"</th><th>"+$("#lang_email").text()+"</th><th>"+$("#lang_operations").text()+"</th></tr></thead><tbody>";
        users.forEach((user) => {
            html+="<tr><td>"+user.id+"</td><td>"+user.username+"</td><td>"+user.fullname+"</td><td>"+user.email+"</td><td><i class=\"fa fa-user-minus\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.removeUser('"+group+"', "+user.id+", this)\"/></td></tr>";
        });
        html+="</tbody></table></div>";

        Modal({
            title: $("#lang_groupMembers").text(),
            content: html,
            buttons: [
                {
                    id: "ok",
                    action: "close",
                    class: "button",
                    label: $("#lang_close").text()
                }
            ]
        });
    });
};

export const removeUser=(group, user, el) => {
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
                url: "./modules/loader.php?load=groups",
                method: "POST",
                data: {removefrom: group, user}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    $(el).parent("td").parent("tr").remove();
                }
            });
        }
    });
};