<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

<div class="container">
<div class="row justify-content-center mt-5">
        <div class="col-5 card p-3">
   <p>Hello,</p>
<span class="mt-1" align="justify">
                We've received a request to reset the psasword for the HR Portal Falcon account associated with {{$data['email']}}.
                No changes have been made to your account yet.
            </span>
            <p>You can reset your password by clicking the link below:</p>
	    <a href="{{route('password.reset-confirm', ['token' => $data['token']])}}" class="btn btn-primary mx-4 my-2">Reset password</a>
            <p>If you didn't make this request, please disregard this email</p>
            <span class="mt-2" align="justify">Please note that your password will not change unless you click the link above. If your link didn't work, you can always request another.</span>
            <span class="mt-2" align="justify">If you've requested multiple reset emails, please make sure you click the link inside the most recent email.</span>
        <p>Web Developer Team</p>
        </div>
    </div>
    <center class="mt-2">
        <small style="color: rgb(68, 68, 68);font-weight: 500;">Copyright {{ date('Y')}} eLink Systems & Concepts Corp.</small>
    </center>
</div> 
