import $ from "jquery";
import NodeRSA from "node-rsa";
import Modal from "./components/Modal.js";

export const toggleRemember=() => {
    $("#remember_checkbox").prop("checked", !$("#remember_checkbox").prop("checked"));
}

export const validateUsername=(el) => {
    $(el).removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");
    if($(el).val()!=""){
        $.ajax({
            url: "./modules/loader.php",
            method: "GET",
            data: {load: "login", sub: "register", username_available: $(el).val()}
        }).then((resp) => {
            $(el).removeClass("checkinput__pending")
            if(resp=="ok"){
                $(el).addClass("checkinput__ok");
            }
            else{
                $(el).addClass("checkinput__error");
            }
        });
    }
};

export const generateKeypair=() => {
    const key=new NodeRSA();
    key.generateKeyPair(4096);

    $("#rsa_public").val(key.exportKey("pkcs1-public-pem"));
    Modal({
        title: $("#lang_rsaPrivate").text(),
        content: $("#lang_rsaPrivateTooltip").text()+"<br/><br/>"+key.exportKey("pkcs1-private-pem").replace(/\n/g, "<br/>"),
        buttons: [
            {
                id: "ok",
                action: "close",
                class: "button button__red",
                icon: "exclamation-triangle",
                label: $("#lang_rsaPrivateTooltip").text()
            }
        ]
    });
};

export const validatePublicKey=(el) => {
    $(el).removeClass("checkinput__ok").removeClass("checkinput__error").addClass("checkinput__pending");
    const key=new NodeRSA();

    try{
        key.importKey($(el).val());
        if(key.isPublic(true)){
            $(el).removeClass("checkinput__pending").addClass("checkinput__ok");
        }
        else{
            $(el).removeClass("checkinput__pending").addClass("checkinput__error");
        }
    }
    catch(e){
        $(el).removeClass("checkinput__pending").addClass("checkinput__ok");
    }
};

export const register=(e) => {
    e.preventDefault();
    console.log($("#register_form").serialize());
};