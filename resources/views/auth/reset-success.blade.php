<!DOCTYPE html>
<html class="no-js">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Minimal and Clean Sign up / Login and Forgot Form by FreeHTML5.co</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Free HTML5 Template by FreeHTML5.co" />
    <meta name="keywords" content="free html5, free template, free bootstrap, html5, css3, mobile first, responsive" />
    <meta name="author" content="FreeHTML5.co" />

    <link rel="shortcut icon" href="favicon.ico">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <form action="#" method="POST" class="fh5co-form animate-box" data-animate-effect="fadeIn">
                    <h2></h2>
                    <div class="form-group">
						<div class="alert alert-success" role="alert">{{$status}}</div>
					</div>
                    
                    {{-- <div class="form-group">
                        <input type="submit" value="Sign In" class="btn btn-primary">
                    </div> --}}
                </form>
                <!-- END Sign In Form -->

            </div>
        </div>
        <div class="row" style="padding-top: 60px; clear: both;">
            <div class="col-md-12 text-center">
                <p><small>&copy; Onemarket 2022 <a href="https://xnodde.com">Designed by Xnodde</a></small></p>
            </div>
        </div>
    </div>



</body>

</html>