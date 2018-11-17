import $ from "jquery";

export const disposeMessage=(el) => {
    $(el).addClass("message__slideOut").delay(1000).slideUp(function(){
        $(this).remove();
    });
};

export const showMessage=(html) => {
    $(html).hide().appendTo("#messageContainer").slideDown();
};

export const loadMessages=() => {
    $.ajax({
        url: "./modules/msg.php",
        type: "GET",
    }).then((resp) => showMessage(resp), (e) => showMessage("<div class=\"message message_error\" onclick=\"ui.main.disposeMessage(this)\"><p>Connection error...</p></div>"));
};

export const route=(site, pop=false) => {
    $("#module").slideUp(function(){
        $.ajax({
            url: "./modules/loader.php",
            type: "GET",
            data: {load: site.split("/")[0], sub: site.split("/")[1]}
        }).then((resp) => {
            loadMessages();

            $("#module").html(resp);
            if(!pop){
                if(history.state!==null && history.state.site.split("/")[1] && history.state.site.split("/")[1]!==""){
                    window.history.pushState({site}, null, "../"+site);
                }
                else{
                    window.history.pushState({site}, null, "./"+site);
                }
            }
            $("#module").slideDown();
        });
    });
};

export const changeLanguage=() => {
    window.location=window.location+"?langstr="+$("#languageSelector").val();
};

export const run=() => {
    window.addEventListener("popstate", (e) => {
        if(e.state!==null){
            route(e.state["site"], true);
        }
        else{
            route("", true);
        }
    });
    loadMessages();
};