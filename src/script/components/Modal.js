import $ from "jquery";

//title: title of modal. Needed
//content: content of modal. Optional
//fields: array of objects. Needed props: id, name. Optional props: type, value
//buttons: array of objects. Needed props: id, action. Optional props: class, icon, label
const Modal=({title, content, fields, buttons}) => {
    return new Promise((resolve, reject) => {
        var root=$("<div class=\"modal\"></div>");
        var body=$("<div class=\"modal__content\"></div>");

        var fieldlist=$("<div class=\"modal__content__fieldlist\"></div>");
        if(fields){
            fields.forEach((field) => {
                $("<p>"+field.name+"</p>").appendTo(fieldlist);
                if(field.type=="textarea"){
                    $("<textarea name=\""+field.id+"\" placeholder=\""+field.name+"...\">"+(field.value?field.value:"")+"</textarea>").appendTo(fieldlist);
                }
                else{
                    $("<input type=\""+(field.type||"text")+"\" name=\""+field.id+"\" placeholder=\""+field.name+"...\" "+(field.value?(field.type=="checkbox"?"checked":"value=\""+field.value+"\""):"")+"/>").appendTo(fieldlist);
                }
            });
        }

        var buttonlist=$("<div></div>");
        if(buttons){
            buttons.forEach((button) => {
                var b=$("<button data-buttonid=\""+button.id+"\"></button>");
                if(button.action=="submit"){
                    b.on("click", function(){
                        var formdata={};
                        $(this).parent("div").parent("div").children("div.modal__content__fieldlist").children("input,textarea").each(function(){
                            formdata[$(this).prop("name")]=$(this).prop("type")=="checkbox"?$(this).prop("checked"):$(this).val();
                        });

                        $(this).parent("div").parent("div").parent("div").fadeOut(function(){
                            $(this).remove();
                        });

                        resolve({button: $(this).data("buttonid"), formdata});
                    });
                }
                else if(button.action=="close"){
                    b.on("click", function(){
                        $(this).parent("div").parent("div").parent("div").fadeOut(function(){
                            $(this).remove();
                        });

                        resolve({button: $(this).data("buttonid")});
                    });
                }
                else{
                    b.on("click", function(){
                        var formdata={};
                        $(this).parent("div").parent("div").children("div.modal__content__fieldlist").children("input,textarea").each(function(){
                            formdata[$(this).prop("name")]=$(this).prop("type")=="checkbox"?$(this).prop("checked"):$(this).val();
                        });
                        button.action({button: $(this).data("buttonid"), formdata});
                    });
                }
                if(button.class){
                    b.prop("class", button.class);
                }
                if(button.icon){
                    b.html("<i class=\"fa fa-"+button.icon+"\"></i> ");
                }
                if(button.label){
                    b.html(b.html()+button.label);
                }

                b.appendTo(buttonlist);
            });
        }

        $("<h2>"+title+"</h2><hr/><div>"+(content||"")+"</div>").appendTo(body);
        fieldlist.appendTo(body);
        buttonlist.appendTo(body);

        body.appendTo(root);

        root.appendTo(document.body);
    });
};

export default Modal;