function readParams(){
    var err=getUrlParameter('err');
    if(validate(err)){
        switch(err){
            case "INVALID_PARAMETERS":
            default:
            err='Something went wrong while processing your request.';
            break;
            case "INVALID_USER_CREDENTIALS":
            err='Invalid credentials. Please verify the details and try again.';
            break;
        }
        $("#message").html('<div class="alert alert-danger"><strong>Error</strong> '+err+'</div>');
    }
    var suc=getUrlParameter("suc");
    if(validate(suc)){
        switch(suc){
            case "ACCOUNT_CREATED":
            suc='Account created successfully. You may login to your account now.';
            break;
        }
        $("#message").html('<div class="alert alert-success"><strong>Success</strong> '+suc+'</div>');
    }
}
