<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->  
  <link rel="stylesheet" href="{{ asset('adminlte/bootstrap/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/AdminLTE.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet"  href="{{ asset('adminlte/plugins/iCheck/square/blue.css')}}">
 
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

<style>
* {
    margin: 0px;
    padding: 0px;
}
html, body {
    height: 100%;
    width: 100%;
    background-color: #ffffff;
    color: #000000;
    font-weight: normal;
    min-width: 500px;
    -ms-overflow-style: -ms-autohiding-scrollbar;
}

#fullPage, #brandingWrapper {
    width: 100%;
    height: 100%;
    background-color: inherit;
}

#brandingWrapper {
    background-color: #8ac76f;
}
#branding {
	
    height: 100%;
    margin-right: 620px;
    margin-left: 0px;
    background-color: inherit;

    background-repeat: no-repeat;
   	
	background-size: cover;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
	
	
	
	
}
.illustrationClass {
    	background-image: url("{{ asset('image/loginBG.jpg') }}");
		background-repeat: no-repeat !important;
	
}
#contentWrapper {
    position: relative;
    width: 620px;
    height: 100%;
    overflow: auto;
    background-color: #ffffff;
    margin-left: -620px;
    margin-right: 0px;
}


.float {
    float: left;
}

.margin-2{
	
	margin: 100px
}

#content {
    min-height: 100%;
    height: auto !important;

}

.divider{
	
	padding-bottom:8vh;
}

.login-box-header4 {
    display: block;
    font-size: 16px;
    font-weight: bolder;
	color:#3c3a3a;
}
.login-box-header3 {
    display: block;
    font-size: 35px;
    font-weight: 700;
	color:#000;
    margin-bottom: 5px !important;
}


.large-space{
	
	font-size: 17px;
	padding-top:25vh;
	
	
}

.btn-green {
    color: #fff;
    background-color: #043fb5;
    border-color: #3b74e6;
    line-height: 1.6;
    font-size: 18px !important;
    font-weight: 700 !important;
  
}
.btn-green:focus,
.btn-green.focus {
  color: #fff;
  background-color: #1e5ede;
  border-color: #366cd8;
}
.btn-green:hover {
  color: #fff;
  background-color: #1e5ede;
  border-color: #366cd8;
}
.btn-green:active,
.btn-green.active,
.open > .dropdown-toggle.btn-green {
  color: #fff;
  background-color: #043fb5;
  border-color: #3b74e6;
}
.btn-green:active:hover,
.btn-green.active:hover,
.open > .dropdown-toggle.btn-green:hover,
.btn-green:active:focus,
.btn-green.active:focus,
.open > .dropdown-toggle.btn-green:focus,
.btn-green:active.focus,
.btn-green.active.focus,
.open > .dropdown-toggle.btn-green.focus {
  color: #fff;
    background-color: #043fb5;
  border-color: #3b74e6;
}
.btn-green:active,
.btn-green.active,
.open > .dropdown-toggle.btn-green {
  background-image: none;
}
.btn-green.disabled:hover,
.btn-green[disabled]:hover,
fieldset[disabled] .btn-green:hover,
.btn-green.disabled:focus,
.btn-green[disabled]:focus,
fieldset[disabled] .btn-green:focus,
.btn-green.disabled.focus,
.btn-green[disabled].focus,
fieldset[disabled] .btn-green.focus {
  background-color: #38a039;
  border-color: #8fd290;
}
.btn-green .badge {
  color: #000;
  background-color: #38a039;
}

.checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
    position: absolute;
    margin-left: 0px !important; 
}

.icheck > label {
    padding-left: 20px !important; 
}
</style>
  
</head>
<body class="hold-transition login-page body">


<div id="fullPage">

	<div id="brandingWrapper" class="float">
		<div id="branding" class="illustrationClass"></div>
	</div>
		
		
    <div id="contentWrapper" class="float">
	<div id="content">
	<div class="divider"></div>	
			  <!-- /.login-logo -->
  <div class="login-box-body">
  <div class="margin-2">
  
    <!--<p class="login-box-msg">Sign-in</p>-->
    <div style="margin-bottom:12px">
		<span class="login-box-header4"><b>Welcome back to SmartReach<b></span>
		<span class="login-box-header3">Login to your account</span>
	</div>
	

	
    @if (Session::has('message'))
           <div class="alert alert-danger">{{ Session::get('message') }}</div>
    @endif
	
	
	
    <form method="POST" action="{{ route('login') }}" >
     @csrf
 
	  
	  
	   <div class="form-group">
		  <label for="exampleInputEmail1">Email</label>
		  <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus />
		  
			@if ($errors->has('email'))
				<span class="invalid-feedback" role="alert" >
					<strong>{{ $errors->first('email') }}</strong>
				</span>
			@endif
		
		</div>
		
		
		<div class="form-group">
		  <label for="exampleInputPassword1">Password</label>
		  <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
		  
		  @if ($errors->has('password'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('password') }}</strong>
				</span>
			@endif
		  
		</div>
	  
	  

	  
      <div class="row">		
        <div class="col-xs-6">
          <div class="checkbox icheck">
        
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

				<label class="form-check-label" for="remember">
					{{ __('Remember Me') }}
				</label>
 
          </div>
        </div> 
        <div class="col-xs-6">
		  <div class="checkbox pull-right">
			<a href="#">Forgot password?</a>
		  </div>
        </div>
      </div>
	  
	  <div class="social-auth-links text-center">
		
		
		 <button type="submit" class="btn btn-green  btn-block  btn-lg">Login now</button>
		</div>
	  
    </form>
	
	<div class="large-space">
	 <!--<span><center>Dont have an account? <a href="{{ route('register') }}" >Join free today</a></center></span>-->
	</div>
  </div>
  </div>
  <!-- /.login-box-body -->
		
		

		</div>
	</div> 
 
 

 </div>



<!-- jQuery 2.2.3 -->
<script  src="{{ asset('public/adminlte/plugins/jQuery/jquery-2.2.3.min.js')}}" ></script>
<!-- Bootstrap 3.3.6 -->
<script  src="{{ asset('public/adminlte/bootstrap/js/bootstrap.min.js')}}" ></script>
<!-- iCheck -->
<script  src="{{ asset('public/adminlte/plugins/iCheck/icheck.min.js')}}"></script>

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>



</body>
</html>