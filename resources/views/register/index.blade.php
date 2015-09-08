@extends('templates.register')

@section('title')
    <?php
    if ($brand == "brightthinker")
        echo "BrightThinker";
    else
        echo "KnowledgeU";
    $brand
    ?> Sales Demo Registration
@stop

@section('body')
    @if($brand == "brightthinker")
        <div class="row">
            <!-- PROFESSOR ED -->
            <div class="col-lg-2">
                <img width="270px" src="{{ url('img/ProfessorEd.png') }}" id="ProfessorEd" alt="Professor Ed Full">
            </div>
            <!-- SUBMISSION FORM -->
            <div class="col-lg-5">
                <div id="SubmissionFormBox" class="roundedBox">
                    <form id="SubmissionForm">
                        <img src="{{ url('img/Logo.png') }}" alt="BrightThinker Logo" class="img-responsive">

                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last name</label>
                            <input class="form-control" type="text" id="lastname" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="domain">Domain name</label>
                            <input class="form-control" type="text" id="domain" name="domain">
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
            </div>
            <!-- TESTIMONIALS -->
            <div class="col-lg-5">
            </div>
        </div>
    @else
    @endif
@stop

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            // OBJECT CENTERING
            centerObjects();

            $(window).on('resize', function () {
                centerObjects();
            })

            function centerObjects() {
                var _window = $(window);
                var _box = $('.row');
                var _marginTop;

                if (_box.height() >= _window.height())
                    _marginTop = 0;
                else
                    _marginTop = Math.floor((_window.height() - _box.height()) / 2);

                _box.css('margin-top', _marginTop + 'px');
            }

            // FORM SUBMISSION HANDLING
            $('#SubmissionForm').submit(function (e) {
                e.preventDefault();
            });
        }, jQuery);
    </script>
@stop