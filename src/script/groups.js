import $ from "jquery";
import Modal from "./components/Modal.js";
import {loadMessages} from "./global.js";

export const getGroups=(event) => {
    //get count
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", groups_count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#grouptable").attr("data-count", count.count);
    });

    //get groups
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "groups", groups: event ? event.data.page : 0}
    }).then((resp) => {
        var groups=JSON.parse(resp);

        groups=groups.map((group) => {
            return Object.assign(group, {
                operations: "<i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.editGroup('"+group.id+"')\"></i><i class=\"fa fa-users\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.getUsersForGroup('"+group.id+"')\"></i>"
            });
        });

        $("#grouptable").attr("data-content", JSON.stringify(groups));
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
                    getGroups();
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

        users=users.map((user) => {
            return Object.assign(user, {
                operations: "<i class=\"fa fa-user-minus\" style=\"margin: 0 0.3em\" onclick=\"ui.groups.removeUser('"+group+"', "+user.id+", this)\"></i>"
            });
        });

        var html="<fancy-table id=\"modal_table\" data-header='[\""+$("#lang_id").text()+"\", \""+$("#lang_username").text()+"\", \""+$("#lang_fullname").text()+"\", \""+$("#lang_email").text()+"\", \""+$("#lang_operations").text()+"\"]' data-order='[\"id\", \"username\", \"fullname\", \"email\", \"operations\"]' data-content=\"[]\" data-nofooter=\"true\"></fancy-table>"

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

        $("#modal_table").attr("data-content", JSON.stringify(users));
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