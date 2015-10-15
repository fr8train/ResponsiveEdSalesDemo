@extends('templates.min')

@section('title','Login')

@section('body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div style="background-color: white; border-radius: 8px; box-sizing: border-box; padding: 1px 13px;">
                    <h1 class="text-center"><i class="fa fa-cog" style="margin-right: 8px;"></i>Login</h1>

                    <form action="{{ url("auth/login") }}" method="post">
                        <div class="form-group">
                            <label for="domain">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            resize();

            $(window).on('resize',resize);
        }, jQuery);

        function resize() {
            var _this = $(".container-fluid");

            _this.css('margin-top',($(window).height()/2) - (_this.height()/2) + "px");
        }
    </script>
@endsection
