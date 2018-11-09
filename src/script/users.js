import $ from "jquery";
import Modal from "./components/Modal.js";
import {loadMessages} from "./global.js";

export const initTable=() => {
    getPageCount();
    loadUsers();
}

export const getPageCount=() => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "users", count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#usertable_count").html(count.count);

        var pages=Math.ceil(count.count/100);

        $("#usertable_pages").html("");
        for(var i=1; i<=pages; i++){
            $("<span onclick=\"ui.users.loadUsers("+(i-1).toString()+")\">"+i.toString()+"</span>").hide().appendTo("#usertable_pages").fadeIn();
        }
    });
};

export const loadUsers=(page=0) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "users", users: page}
    }).then((resp) => {
        var users=JSON.parse(resp);

        var html="";
        users.forEach((user) => {
            html+="<tr><td>"+user.id+"</td><td>"+user.username+"</td><td>"+user.fullname+"</td><td>"+(user.groups||"")+"</td><td>"+user.country+"</td><td>"+user.region+"</td><td>"+user.city+"</td><td>"+user.address+"</td><td>"+user.phone+"</td><td>"+user.email+"</td><td>"+"<i class=\"fa fa-key\" style=\"margin: 0 0.3em\" onclick=\"ui.users.newPassword("+user.id+")\"/><i class=\"fa fa-users\" style=\"margin: 0 0.3em\" onclick=\"ui.users.editGroups("+user.id+")\"/>"+"</td></tr>";
        });
        
        $("#usertable_content").html(html);
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
                    initTable();
                });
            }
        });
    });
};