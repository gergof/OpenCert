import $ from "jquery";
import marked from "marked";
import {loadMessages} from "./global.js";
import Modal from "./components/Modal.js";

export const getNews=(event) => {
    //get count
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "news", news_count: true}
    }).then((resp) => {
        var count=JSON.parse(resp);

        $("#newstable").attr("data-count", count.count);
    });

    //get data
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "news", news: event ? event.data.page : 0}
    }).then((resp) => {
        var news=JSON.parse(resp);

        news=news.map((pnews) => {
            return Object.assign(pnews, {
                operations: "<i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.news.editNews("+pnews.id+")\"></i><i class=\"fa fa-users\" style=\"margin: 0 0.3em\" onclick=\"ui.news.editTarget("+pnews.id+")\"></i><i class=\"fa fa-trash\" style=\"margin: 0 0.3em\" onclick=\"ui.news.deleteNews("+pnews.id+")\"></i>"
            });
        });

        $("#newstable").attr("data-content", JSON.stringify(news));
    });
};

const openPreview=({formdata})=>{
    Modal({
        title: $("#lang_preview").text(),
        content: marked(formdata.content),
        buttons: [
            {
                id: "close",
                action: "close",
                class: "button",
                icon: "times",
                label: $("#lang_close").text()
            }
        ]
    });
};

export const newNews=() => {
    Modal({
        title: $("#lang_createNew").text(),
        content: $("#lang_markdownTooltip").text(),
        fields: [
            {
                id: "title",
                name: $("#lang_title").text()
            },
            {
                id: "content",
                name: $("#lang_content").text(),
                type: "textarea"
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
            },
            {
                id: "preview",
                action: openPreview,
                class: "button",
                icon: "eye",
                label: $("#lang_preview").text()
            }
        ]
    }).then((resp) => {
        if(resp.button=="ok"){
            $.ajax({
                url: "./modules/loader.php?load=news",
                method: "POST",
                data: {new: JSON.stringify(resp.formdata)}
            }).then((resp) => {
                loadMessages();
                getNews();
            });
        }
    });
};

export const editNews=(id) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "news", getnews: id}
    }).then((resp) => {
        loadMessages();
        if(resp=="error"){
            return;
        }

        var news=JSON.parse(resp);

        Modal({
            title: $("#lang_edit").text(),
            content: $("#lang_markdownTooltip").text(),
            fields: [
                {
                    id: "title",
                    name: $("#lang_title").text(),
                    value: news.title
                },
                {
                    id: "content",
                    name: $("#lang_content").text(),
                    type: "textarea",
                    value: news.content
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
                },
                {
                    id: "preview",
                    action: openPreview,
                    class: "button",
                    icon: "eye",
                    label: $("#lang_preview").text()
                }
            ]
        }).then((resp) => {
            if(resp.button=="ok"){
                $.ajax({
                    url: "./modules/loader.php?load=news",
                    method: "POST",
                    data: {update: JSON.stringify(Object.assign(resp.formdata, {id: id}))}
                }).then((resp) => {
                    loadMessages();
                    getNews();
                });
            }
        });
    });
};

export const deleteNews=(id) => {
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
                url: "./modules/loader.php?load=news",
                method: "POST",
                data: {delete: id}
            }).then((resp) => {
                loadMessages();
                getNews();
            });
        }
    });
};

export const editTarget=(id) => {
    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "news", gettarget: id}
    }).then((resp) => {
        var targets=JSON.parse(resp);

        var fields=[];
        targets.allGroups.forEach((g) => {
            var value=false;
            for(var i=0; i<targets.targetFor.length; i++){
                if(targets.targetFor[i].group==g.group){
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
            title: $("#lang_setTargetFor").text(),
            fields,
            buttons: [
                {
                    id: "ok",
                    action: "submit",
                    class: "button button__green",
                    icon: "check",
                    label: $("lang_ok").text()
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
                var targets=Object.keys(resp.formdata).filter((cur) => resp.formdata[cur]);

                $.ajax({
                    url: "./modules/loader.php?load=news",
                    method: "POST",
                    data: {new_targets: id, targets: JSON.stringify(targets)}
                }).then((resp) => {
                    loadMessages();
                    getNews();
                });
            }
        });
    });
};