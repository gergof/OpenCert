import $ from "jquery";
import Modal from "./components/Modal.js";
import {loadMessages} from "./global.js";

export const getUsers=(event) => {
    //get count
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "users", users_count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#usertable").attr("data-count", count.count);
    });

    //get users
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "users", users: event ? event.data.page : 0}
    }).then((resp) => {
        var users=JSON.parse(resp);

        users=users.map((user) => {
            return Object.assign(user, {
                operations: "<i class=\"fa fa-key\" style=\"margin: 0 0.3em\" onclick=\"ui.users.newPassword("+user.id+")\"></i><i class=\"fa fa-users\" style=\"margin: 0 0.3em\" onclick=\"ui.users.editGroups("+user.id+")\"></i>"
            });
        });

        $("#usertable").attr("data-content", JSON.stringify(users));
    });
};

export const newPassword=(id) => {
    Modal({
        title: $("#lang_newPassword").text(),
        fields: [
            {
                id: "password",
                name: $("#lang_password").text(),
                type: "password"
            },
            {
                id: "password_conf",
                name: $("#lang_passwordConf").text(),
                type: "password"
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
                url: "./modules/loader.php?load=users",
                method: "POST",
                data: {new_passwd: id, passwd: resp.formdata.password, passwd_conf: resp.formdata.password_conf}
            }).then((resp) => {
                loadMessages();
            });
        }
    });
};

export const editGroups=(id) => {
    //get groups
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "users", groupsfor: id}
    }).then((resp) => {
        var groups=JSON.parse(resp);

        var fields=[];
        groups.allGroups.forEach((g) => {
            var value=false;
            for(var i=0; i<groups.memberOf.length; i++){
                if(groups.memberOf[i].group==g.group){
                    value=true;
                    break;
                }
            }

            fields.push({
                id: g.group,
                name: g.group+":",
                type: "checkbox",
                value
            });
        });

        Modal({
            title: $("#lang_setMemberOf").text(),
            fields,
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
                var groups=Object.keys(resp.formdata).filter((cur) => resp.formdata[cur]);

                $.ajax({
                    url: "./modules/loader.php?load=users",
                    method: "POST",
                    data: {new_groups: id, groups: JSON.stringify(groups)}
                }).then((resp) => {
                    loadMessages();
                    getUsers();
                });
            }
        });
    });
};