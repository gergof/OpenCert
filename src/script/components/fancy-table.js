import $ from "jquery";
import template from "./templates/fancy-table.html";
import style from "!raw-loader!sass-loader!./styles/fancy-table.scss";

//needed args:
//data-countlabel: (optional) Label to show for count
//data-count: Number of entries
//data-perpage: How many entries on a page
//data-header: Json array representing the header
//data-order: Json array representing the field names of content object in the order to be displayed
//data-content: Json array or javascript object representing the data to display
//data-requestpage: Event; The user requested the page n to be fetched. It should update these attributes: data-count, data-content. If event object is null it should fetch the 1st page
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

        //create jquery root
        this.$=(el) => $(this.shadowRoot.querySelector(el));
    }

    initTable(){
        //localization
        this.$("#table_count_label").html(this.dataset.countlabel||"Count: ");

        //request data
        eval(this.dataset.requestpage)(null);

        //load components
        this.loadHeader();
        this.loadData();
        this.loadPageCount();
    }

    loadHeader(){
        var header=JSON.parse(this.dataset.header);

        header.map((field) => {
            var f=$("<th></th>");
            f.html(field);
            f.appendTo(this.$("#table_header"));
        });
    }

    loadData(){
        var order=JSON.parse(this.dataset.order);
        var data=JSON.parse(this.dataset.content);

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

        this.$("#table_content").html(newBody.html());
    }

    loadPageCount(){
        var count=this.dataset.count;
        var perpage=this.dataset.perpage;

        this.$("#table_count").text(count);

        this.$("#table_pages").html("");

        for(var i=1; i<=Math.ceil(count/perpage); i++){
            var p=$("<span></span>");
            p.html(i.toString());
            p.on("click", {page: i-1}, eval(this.dataset.requestpage));
            p.hide().appendTo(this.$("#table_pages")).fadeIn();
        }
    }

    connectedCallback(){
        this.initTable();
    }

    attributeChangedCallback(name, oldVal, newVal){
        if(name=="data-count" && oldVal!==newVal){
            this.loadPageCount();
        }
        if(name=="data-content" && oldVal!==newVal){
            this.loadData();
        }
    }

    static get observedAttributes(){
        return ["data-content", "data-count"];
    }
}

customElements.define("fancy-table", FancyTable);