import $ from "jquery";
import marked from "marked";

export const getNews=(offset=0) => {
    //remove current loading button
    $("#news_loadmore").fadeOut(function(){
        $(this).remove();
    });

    $.ajax({
        url: "./modules/loader.php",
        method: "GET",
        data: {load: "", news: offset}
    }).then((resp) => {
        var news=JSON.parse(resp);
        console.log(news);
        
        news.forEach((n) => {
            var root=$("<div style=\"margin-bottom: 2em\"></div>");
            var header="<h2>"+n.title+"</h2>"+"<span style=\"font-size: 0.8em\">"+n.publish+"<br/>By: <i>"+n.user+"</i></span><hr/>";
            var body="<div>"+marked(content)+"</div>";
            root.html(header+body+"<hr/>");

            root.hide().appendTo("#news").slideDown();
        });

        //add the new loadMore button
        $("<button class=\"button\" onclick=\"ui.index.getNews("+(offset+10)+")\">"+$("#lang_loadmore").text()+"</button>").hide().appendTo("#news").slideDown();
    });
};