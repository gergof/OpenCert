import $ from "jquery";
import template from "./templates/reputation-slider.html";
import style from "!raw-loader!sass-loader!./styles/reputation-slider.scss";

//args:
//data-max: maximum value on the slider (defaults to 100)
//data-value: current value
class ReputationSlider extends HTMLElement{
    constructor(){
        super();

        //init shadow DOM
        this.attachShadow({mode: "open"});
        this.shadowRoot.innerHTML="<style>"+style+"</style>"+template;

        //create jQuery root
        this.$=(el) => $(this.shadowRoot.querySelector(el));

        var percentage=this.dataset.value*100/(this.dataset.max||100);
        this.$(".reputationSlider__pointer__container").css("left", percentage+"%");
    }
}

customElements.define("reputation-slider", ReputationSlider);