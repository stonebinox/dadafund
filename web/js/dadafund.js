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
var app=angular.module("dadafund",[]);
app.config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});
app.controller("account",function($scope,$http,$compile){
    $scope.userArray=[];
    $scope.getUser=function(){
        $http.get("user/getUser")
        .then(function success(response){
            response=response.data;
            console.log(response);
            if(typeof response=="object"){
                $scope.userArray=response;
                $scope.userName=stripslashes($scope.userArray.user_name);
            }
            else{
                response=$.trim(response);
                switch(response){
                    case "INVALID_PARAMETERS":
                    default:
                    messageBox("Problem","Something went wrong while loading some infotrmation. Please try again later. This is the error we see: "+response);
                    break;
                    case "INVALID_USER_ID":
                    messageBox("Invalid User","You are an invalid user. Please refresh the page and try again.");
                    break;
                }
            }
        },
        function error(response){
            console.log(response);
            messageBox("Problem","Something went wrong while loading some information. Please try again later.");
        });
    };
    $scope.transactionArray=[];
    $scope.transactionOffset=0;
    $scope.getTransactions=function(){
        $http.get("transaction/getAll?offset="+$scope.transactionOffset)
        .then(function success(response){
            response=response.data;
            console.log(response);
            if(typeof response=="object"){
                $scope.transactionArray=response;
                $scope.displayTransactions();
            }
            else{
                response=$.trim(response);
                switch(response){
                    case "INVALID_PARAMETERS":
                    default:
                    messageBox("Problem","Something went wrong while loading your past transactions. Please try again later. This is the response we see: "+response);
                    break;
                    case "NO_TRANSACTIONS_FOUND":
                    $("#transactionlist").html('<p class="text-center">No data found.</p>');
                    break;
                }
            }
        },
        function error(response){
            console.log(response);
            messageBox("Problem","Something went wrong while loading your past transactions. Please try again later.");
        });
    };  
    $scope.displayTransactions=function(){
        if($scope.transactionArray.length>0){
            var transactions=$scope.transactionArray;
            $("#transactionlist").html('<h3 class="text-center">Recent transactions</h3>');
            var text='<table class="table"><thead><tr><th>Location</th><th>Billed amount</th><th>Donatable amount</th></tr></thead><tbody>';
            for(var i=0;i<transactions.length;i++){
                var transaction=transactions[i];
                var transactionID=transaction.idtransaction_master;
                var partner=transaction.partner_master_idpartner_master;
                var partnerName=partner.partner_name;
                var originalAmount=transaction.original_amount;
                var donation=transaction.amount_difference;
                text+='<tr><td>'+partnerName+'</td><td>'+originalAmount+'</td><td><span class="text-success">'+donation+'</span></td></tr>';
            }
            text+='</tbody></table>';
        }
        $("#transactionlist").append(text);
    };
});
