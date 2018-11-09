import $ from "jquery";

export const toggleRemember=() => {
    $("#remember_checkbox").prop("checked", !$("#remember_checkbox").prop("checked"));
}