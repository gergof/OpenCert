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
                operations: "<i class=\"fa fa-edit\" style=\"margin: 0 0.3em\" onclick=\"ui.news.editNews("+pnews.id+")\"/><i class=\"fa fa-trash\" style=\"margin: 0 0.3em\" onclick=\"ui.news.deleteNews("+pnews.id+")\"/>"
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
        $.ajax({
            url: "./modules/loader.php?load=news",
            method: "POST",
            data: {new: JSON.stringify(resp.formdata)}
        }).then((resp) => {
            loadMessages();
            getNews();
        });
    });
};