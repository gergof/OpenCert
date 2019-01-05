import $ from "jquery";
import NodeRSA from "node-rsa";
import Modal from "./components/Modal.js";
import {route, loadMessages} from "./global.js";

export const validateName=(el) => {
    var cont=$(el).parent(".checkinput");
    cont.removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");
    if($(el).val()!=""){
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "myorg", name_available: $(el).val()}
        }).then((resp) => {
            cont.removeClass("checkinput__pending");
            if(resp=="ok"){
                cont.addClass("checkinput__ok");
            }
            else{
                cont.addClass("checkinput__error");
            }
        });
    }
};

export const validatePublicKey=(el) => {
    var cont=$(el).parent(".checkinput");
    cont.removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");

    const key=new NodeRSA();

    try{
        key.importKey($(el).val());
        if(key.isPublic(true)){
            cont.removeClass("checkinput__pending").addClass("checkinput__ok");
        }
        else{
            cont.removeClass("checkinput__pending").addClass("checkinput__error");
        }
    }
    catch(e){
        cont.removeClass("checkinput__pending").addClass("checkinput__error");
    }
};

export const generateKeypair=() => {
    const key=new NodeRSA();
    key.generateKeyPair(4096);

    $("#rsa_public").val(key.exportKey("pkcs1-public-pem"));
    $("#rsa_public").trigger("input");

    Modal({
        title: $("#lang_rsaPrivate").text(),
        content: $("#lang_rsaPrivateTooltip").text()+"<br/><br/>"+key.exportKey("pkcs1-private-pem").replace(/\n/g, "<br/>"),
        buttons: [
            {
                id: "ok",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_rsaPrivateWrotedown").text()
            }
        ]
    });
};

export const newOrg=(e) => {
    e.preventDefault();

    var data={};
    $("#neworg_form").serializeArray().map((cur) => {
        data[cur.name]=cur.value;
    });

    $.ajax({
        url: "./modules/loader.php?load=myorg",
        method: "POST",
        data: {neworg: JSON.stringify(data)}
    }).then((resp) => {
        loadMessages();
        if(resp=="ok"){
            route("myorg");
        }
    });
};

export const editOrg=() => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "myorg", myorg: true}
    }).then((resp) => {
        loadMessages();
        if(resp!="error"){
            var data=JSON.parse(resp);

            Modal({
                title: $("#lang_edit").text(),
                fields: [
                    {
                        id: "name",
                        name: $("#lang_name").text(),
                        value: data.name
                    },
                    {
                        id: "region",
                        name: $("#lang_region").text(),
                        value: data.region
                    },
                    {
                        id: "city",
                        name: $("#lang_city").text(),
                        value: data.city
                    },
                    {
                        id: "address",
                        name: $("#lang_address").text(),
                        value: data.address
                    },
                    {
                        id: "phone",
                        name: $("#lang_phone").text(),
                        value: data.phone
                    },
                    {
                        id: "email",
                        name: $("#lang_email").text(),
                        type: "email",
                        value: data.email
                    },
                    {
                        id: "bio",
                        name: $("#lang_bio").text(),
                        type: "textarea",
                        value: data.bio
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
                $.ajax({
                    url: "./modules/loader.php?load=myorg",
                    method: "POST",
                    data: {edit: JSON.stringify(resp.formdata)}
                }).then((resp) => {
                    loadMessages();
                });
            });
        }
    });
};

export const leave=() => {
    Modal({
        title: $("#lang_leaveSure").text(),
        buttons: [
            {
                id: "leave",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_leaveorg").text()
            },
            {
                id: "cancel",
                action: "close",
                class: "button button__green",
                label: $("#lang_cancel").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="leave"){
            $.ajax({
                url: "./modules/loader.php?load=myorg",
                method: "POST",
                data: {leave: true}
            }).then((resp) => {
                loadMessages();
                if(resp=="ok"){
                    route("myorg");
                }
            });
        }
    });
};