import $ from "jquery";
import NodeRSA from "node-rsa";
import Modal from "./components/Modal.js";
import {route, loadMessages} from "./global.js";

export const toggleRemember=() => {
    $("#remember_checkbox").prop("checked", !$("#remember_checkbox").prop("checked"));
}

export const validateUsername=(el) => {
    var cont=$(el).parent(".checkinput");
    cont.removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");
    if($(el).val()!=""){
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "login", sub: "register", username_available: $(el).val()}
        }).then((resp) => {
            cont.removeClass("checkinput__pending")
            if(resp=="ok"){
                cont.addClass("checkinput__ok");
            }
            else{
                cont.addClass("checkinput__error");
            }
        });
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

export const validatePassword=(el) => {
    var cont=$(el).parent(".checkinput");
    cont.removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");

    if($("#password").val()==$(el).val()){
        cont.removeClass("checkinput__pending").addClass("checkinput__ok");
    }
    else{
        cont.removeClass("checkinput__pending").addClass("checkinput__error");
    }
};

export const register=(e) => {
    e.preventDefault();

    var data={};
    $("#register_form").serializeArray().map((cur) => {
        data[cur.name]=cur.value;
    });

    $.ajax({
        url: "../modules/loader.php?load=login&sub=register",
        method: "POST",
        data: {register: JSON.stringify(data)}
    }).then((resp) => {
        if(resp=="ok"){
            route("login");
        }
        else{
            loadMessages();
        }
    });
};