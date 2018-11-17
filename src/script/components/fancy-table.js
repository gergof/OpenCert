import $ from "jquery";
import template from "./templates/fancy-table.html";
import style from "!raw-loader!sass-loader!./styles/fancy-table.scss";

//needed args:
//data-countLabel: (optional) Label to show for count
//data-count: Number of entries
//data-perpage: How many entries on a page
//data-header: Json array representing the header
//data-order: Json array representing the field names of content object in the order to be displayed
//data-content: Json array or javascript object representing the data to display
//data-requestPage: Event; The user requested the page n to be fetched
class FancyTable extends HTMLElement{
    constructor(){
        super();

        //init shadow DOM
        this.attachShadow({mode: "open"});
        this.shadowRoot.innerHTML="<style>"+style+"</style>"+template;

        //bind this to member functions
        this.initTable=this.initTable.bind(this);
        this.loadHeader=this.loadHeader.bind(this);
        this.loadPageCount=this.loadPageCount.bind(this);
        this.loadData=this.loadData.bind(this);
        this.refresh=this.refresh.bind(this);
    }

    initTable(){
        //localization
        this.shadowRoot.querySelector("#table_count_label").innerHTML=this.dataset.countLabel||"Count: ";

        //load components
        this.loadHeader();
        this.loadData();
        this.loadPageCount();
    }

    loadHeader(){
        var header=JSON.parse(this.dataset.header);

        header.map((field) => {
            var f=$("<th></th");
            f.html(field);
            f.appendTo(this.$.table_header);
        });
    }

    loadData(){
        var order=JSON.parse(this.dataset.order);
        var data=this.dataset.content instanceof Object ? this.dataset.content : JSON.parse(this.dataset.content);

        var oldBody=this.shadowRoot.querySelector("#table_content");
        var newBody=$("<tbody></tbody>");

        data.map((row) => {
            var r=$("<tr></tr>");

            order.forEach((field) => {
                var f=$("<td></td>");
                f.html(row[field]);
                f.appendTo(r);
            });

            r.appendTo(newBody);
        });

        this.shadowRoot.querySelector("#table").replaceChild(newBody, oldBody);
    }

    loadPageCount(){
        var count=this.dataset.count;
        var perpage=this.dataset.perpage;

        this.shadowRoot.querySelector("#table_count").innerHTML(count);

        var el=$(this.$.table_pages);
        el.html("");

        for(var i=1; i<=Math.ceil(count/perpage); i++){
            var p=$("<span></span>");
            p.html(i.toString());
            p.on("click", {page: i-1}, this.dataset.requestPage);
            p.hide().appendTo(el).fadeIn();
        }
    }

    connectedCallback(){
        this.initTable();
    }

    attributeChangedCallback(){
        this.loadData();
        this.loadPageCount();
    }
}

customElements.define("fancy-table", FancyTable);