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

//login.js
import * as mod_login from "./login.js";
export const login=mod_login;

//users.js
import * as mod_users from "./users.js";
export const users=mod_users;

//groups.js
import * as mod_groups from "./groups.js";
export const groups=mod_groups;