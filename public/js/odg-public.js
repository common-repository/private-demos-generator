jQuery(document).ready(function ($) {

	$.fn.extend({
        inlineValidationEngine: function(options,arg) {
            if (options && typeof(options) == 'object') {
                options = $.extend( {}, $.inlineValidationEngine.defaults, options );
            }

            // this creates a plugin for each element in
            // the selector or runs the function once per
            // selector.  To have it do so for just the
            // first element (once), return false after
            // creating the plugin to stop the each iteration 
            this.each(function() {
                new $.inlineValidationEngine(this, options, arg );
            });
            return;
        }
    });
    
    $.inlineValidationEngine = function( elem, options, arg ) {        
        options["autoHidePrompt"]= true;
        options["autoHideDelay"]= 1;
        options["binded"]= false;
        options["scroll"]= false;
        options["focusFirstField"]=false;
        var error_messages_area=$(elem).attr("data-form-failure-messages");
		//console.log(error_messages_area);
        if(error_messages_area)
        {
            error_messages_area=$(error_messages_area);
            //On d�tecte lorsque la zone de notification change
            error_messages_area.bind('DOMNodeInserted', function(event) {
                var fixed_menu_height=$("#header-rows").height();
                var window_width=$(window).width();
                var scrollTo=scrollTo=error_messages_area.offset().top-error_messages_area.height();
                if(window_width>=900)//Taille ou le menu est fixe
                     scrollTo=scrollTo-fixed_menu_height;
                $(window).scrollTop(scrollTo);
            });
        }
        $(elem).validationEngine('attach',options);
     
        $(elem).bind("jqv.form.validating", function(e) {
//            =$("#"+e.target.id).attr("data-form-failure-messages");
            $(error_messages_area).html('');
        });

        $(elem).bind("jqv.field.result", function(e, field, errorFound, prompText) {
            if(errorFound&&prompText!="false<br/>")
            {
                field.addClass("invalid-entry");
                if(error_messages_area)
                {                
                    var message="<span class'"+field.context.id+"'>"+prompText+"</span>";

                    if(error_messages_area.children(".alert-error").length>0)
                    {
                        var prompt_array=prompText.split('<br/>');
                        $.each( prompt_array, function( key, checkable_prompt ) {
                            if(checkable_prompt=='<br/>')
                                return;
                            var errors=error_messages_area.html();
                            if(errors.indexOf(checkable_prompt) < 0)
                                error_messages_area.children(".alert-error").append(message);
                        });

                    }
                    else
                    {
                        error_messages_area.html('<div class="alert alert-error" style="border-radius: 0;">'+message+'</div>');
                        
                    }
                
                }
                //When there is no area to display errors
                else
                {
                    var prompt_array=prompText.split('<br/>');
                    $.each( prompt_array, function( key, checkable_prompt ) {
                        if(checkable_prompt=='<br/>' ||checkable_prompt=='')
                            return;
                        alert(checkable_prompt);
                    });
                }
                
            }
            else
                field.removeClass("invalid-entry");
        });

    };

	$("#demo-request-frm").inlineValidationEngine({
        'custom_error_messages': {
            '#demo-request-email' : {
                'required': {
                    'message': "Email required"
                },
                'custom[email]': {
                    'message': "Email format incorrect"
                }
            }
        },
        onValidationComplete: process_demo_install
    });
    
    function process_demo_install(form, status)
    {
            $("#debug").html("");
            if(status===true){
                $(".frm-loader").show();
                var demo_id=$("#demo-id").val();
                var email=$("#demo-request-email").val();
                
                $.post(
                        odg_ajax_object.ajax_url,
                        {
                            action: "pdg_generate_demo_install", 
                            demo_id:demo_id,
                            email: email,
                        },
                        function(data) {
                            $(".frm-loader").hide();
                            if(is_json(data))
                            {
                                var response=JSON.parse(data);
                                if(response.success)
                                    $("#debug").html(response.message);
                            }
                            else
                                $("#debug").html(data);
                        }
                    );
            }
         
    }

});
function is_json(data)
    {
        if (/^[\],:{}\s]*$/.test(data.replace(/\\["\\\/bfnrtu]/g, '@').
        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
        replace(/(?:^|:|,)(?:\s*\[)+/g, '')))
            return true;
        else
            return false;
    }
