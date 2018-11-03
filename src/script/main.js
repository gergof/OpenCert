//import webcomponents
import "@webcomponents/webcomponentsjs";
import "./components/main.js";

//import styles
import "../style/main.scss";



///
///import modules
///

//global.js
import * as mod_main from "./global.js";
export const main=mod_main;
main.run();

//index.js
import * as mod_index from "./index.js";
export const index=mod_index;