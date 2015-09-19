@extends('templates.register')

@section('title')
    <?php
    if ($brand == "brightthinker")
        echo "BrightThinker";
    else
        echo "KnowledgeU";
    ?> Sales Demo Registration
@stop

@section('body')
    <div id="progressBarBox" class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="roundedBox" style="background-color: #FFF; padding: 20px;">
                <h4 id="statusMessage" class="text-center" style="margin-top: 20px;"></h4>

                <div class="progress" style="margin-bottom: 0px">
                    <div id="statusBar" class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
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

    <script type="text/javascript">
        // POST VARIABLES
        var firstName = '<?= $firstname ?>';
        var lastName = '<?= $lastname ?>';
        var email = '<?= $email ?>';
        var password = '<?= $password ?>';
        var domainName = '<?= $domain ?>';
        var parentDomainId = parseInt(<?= $parent_domain_id ?>);

        var statusMessage = $("#statusMessage");
        var statusBar = $("#statusBar");

        var startTime = null;

        // AJAX VARIABLES
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        });

        createDomain();

        function createDomain() {
            startTime = new Date();
            statusMessage.html("Creating domain " + domainName + "...");
            statusBar.css('width','20%').attr('aria-valuenow',20);
            dlap('{{ url('dlap/create-domain') }}',
                    {
                        domainName: domainName,
                        parentDomainId: parentDomainId,
                        key: '<?= $key ?>'
                    }, function(jqXHR, textStatus) {
                        var time = new Date() - startTime;

                        console.log(jqXHR);

                        if (jqXHR.status == 200) {
                            time = time < 1500 ? 1500 - time : 0;

                            setTimeout(function () {
                                var response = JSON.parse(jqXHR.responseText);
                                navigateToNewDomain(response.payload.userspace);
                            }, time);
                        }
                    });
        }

        function createUserAccount() {
        }

        function navigateToNewDomain(userspace) {
            console.log(userspace);
            statusMessage.empty().html("Navigating to new domain...");
            statusBar.css("width","100%").attr("aria-valuenow",100);
            window.location.href = "http://" + userspace + ".agilixbuzz.com";
        }

        function dlap(uri,_data, func) {
            $.ajax({
                url: uri,
                method: "POST",
                data: JSON.stringify(_data)
            }).complete(function(jqXHR, textStatus){
                func(jqXHR, textStatus);
            });
        }
    </script>
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