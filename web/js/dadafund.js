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
            case "INVALID_USER_NAME":
            err='Invalid user name. Please enter your full name and try again.';
            break;
            case "INVALID_USER_EMAIL":
            err='Invalid email ID. Please enter a valid email ID and try again.';
            break;
            case "INVALID_PASSWORD":
            err='Invalid password. Please ensure the password is at least 8 characters in length.';
            break;
            case "PASSWORD_MISMATCH":
            err='Password mismatch. Please ensure the passwords match each other.';
            break;
            case "ACCOUNT_ALREADY_EXISTS":
            err='Account already exists. <a href="https://dusthq-dadafund.herokuapp.com/">Login</a> to your account.';
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
