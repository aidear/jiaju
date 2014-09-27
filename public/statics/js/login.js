
$(function(){
    $('form').submit(function(){
        var validator = new Validator;
        validator.username($("input[name='username']"));
        validator.notEmpty($("input[name='password']"));
        if(!validator.isValid){
            return false;
        }
    });
    $("input[name='username'],input[name='password']", $("form.form-login")).focusin(function(){$(this).next('span.error').html('');});
    $("#loginCode", $("form.form-login")).focusin(function(){$(".alert p").html('');});
});