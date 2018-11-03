import template from "./templates/fancy-checkbox.html";
import style from "!raw-loader!sass-loader!./styles/fancy-checkbox.scss";

class FancyCheckbox extends HTMLElement{
    constructor(){
        super();

        this.attachShadow({mode: "open"});
        this.shadowRoot.innerHTML="<style>"+style+"</style>"+template;

        this.shadowRoot.querySelector(".checkbox").addEventListener("click", this.toggle.bind(this));
    }

    toggle(){
        this.shadowRoot.querySelector(".checkbox__toggle").classList.toggle("checkbox__toggle__active");
        this.onchange();
    }
}

customElements.define("fancy-checkbox", FancyCheckbox);