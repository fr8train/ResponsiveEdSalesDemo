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
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        });

        function createDomain() {
        }

        function createUserAccount() {
        }
    </script>
    <div id="progressBarBox" class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="roundedBox" style="background-color: #FFF; padding: 20px;">
                <h4 class="text-center" style="margin-top: 20px;">Creating user account...</h4>

                <div class="progress" style="margin-bottom: 0px">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60"
                         aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                        <span class="sr-only">60% Complete (warning)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="position: relative;top: -220px;">
        <div class="col-sm-8 col-sm-offset-2">
            @if($brand == "brightthinker")
                <img src="{{ url('img/ProfessorEd-headshot.png') }}" width="190" class="img-responsive center-block" alt="Box Logo">
            @else
                <img src="{{ url('img/KnowledgeUBox.png') }}" width="190" class="img-responsive center-block" alt="Box Logo">
            @endif
        </div>
    </div>
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
                var _box = $('#progressBarBox.row');
                var _marginTop;

                if (_box.height() >= _window.height())
                    _marginTop = 0;
                else
                    _marginTop = Math.floor((_window.height() - _box.height()) / 2);

                _box.css('margin-top', _marginTop + 'px');
            }
        }, jQuery);
    </script>
@stop