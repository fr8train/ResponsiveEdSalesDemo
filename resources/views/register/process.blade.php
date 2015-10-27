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
                    <div id="statusBar" class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="0"
                         aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="position: relative;top: -220px;">
        <div class="col-sm-8 col-sm-offset-2">
            @if($brand == "brightthinker")
                <img src="{{ url('img/ProfessorEd-headshot.png') }}" width="190" class="img-responsive center-block"
                     alt="Box Logo">
            @else
                <img src="{{ url('img/KnowledgeUBox.png') }}" width="190" class="img-responsive center-block"
                     alt="Box Logo">
            @endif
        </div>
    </div>

    <!-- USER INFO MODAL -->
    <div id="UserInfoModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h2 class="modal-title">User Info</h2>

                    <p>Please take time to write down the user login credentials displayed below: </p>
                    <dl class="dl-horizontal">
                        <h4>Student Info</h4>
                        <dt>Username</dt>
                        <dd id="StudentUsername"></dd>
                        <dt>Password</dt>
                        <dd id="StudentPassword"></dd>
                        <h4>Teacher Info</h4>
                        <dt>Username</dt>
                        <dd id="TeacherUsername"></dd>
                        <dt>Password</dt>
                        <dd id="TeacherPassword"></dd>
                    </dl>
                    <button type="button" onclick="consumeUserInfo()" style="white-space: normal;"
                            class="btn center-block submitBtn">I have saved
                        the user info and want to
                        continue the registration process.
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script type="text/javascript">
        // POST VARIABLES
        var firstName = '<?= $firstname ?>';
        var lastName = '<?= $lastname ?>';
        var email = '<?= $email ?>';
        var password = '<?= $password ?>';
        var domainName = '<?= $domain ?>';
        var phone = '<?= $reference ?>';
        var parentDomainId = parseInt(<?= $parent_domain_id ?>);

        var statusMessage = $("#statusMessage");
        var statusBar = $("#statusBar");

        var startTime = null;
        var registeredUserSpace = null;

        var student = null;
        var teacher = null;

        var consumedUserInfoGate = false;

        // AJAX VARIABLES
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        });

        $("#UserInfoModal").on('hidden.bs.modal', function (e) {
            if (consumedUserInfoGate)
                $("#UserInfoModal").modal("hide");
        })

        createDomain();

        function createDomain() {
            startTime = new Date();
            statusMessage.html("Creating domain " + domainName + "...");
            dlap('{{ url('dlap/create-domain') }}',
                    {
                        domainName: domainName,
                        parentDomainId: parentDomainId,
                        key: '<?= $key ?>'
                    }, function (jqXHR, textStatus) {
                        var time = new Date() - startTime;

                        if (jqXHR.status == 200) {
                            time = time < 1500 ? 1500 - time : 0;

                            setTimeout(function () {
                                var response = JSON.parse(jqXHR.responseText);
                                console.log(response);
                                statusBar.css('width', '20%').attr('aria-valuenow', 20);
                                registeredUserSpace = response.payload.userspace;
                                createUserAccount();
                            }, time);
                        }
                    });
        }

        function createUserAccount() {
            startTime = new Date();
            statusMessage.html("Creating user accounts...");
            dlap('{{ url('dlap/create-users') }}',
                    {
                        userspace: registeredUserSpace,
                        parentDomainId: parentDomainId,
                        firstname: firstName,
                        lastname: lastName,
                        email: email,
                        password: password,
                        reference: phone,
                        key: '<?= $key ?>'
                    }, function (jqXHR, textStatus) {
                        var time = new Date() - startTime;

                        if (jqXHR.status == 200) {
                            time = time < 1500 ? 1500 - time : 0;

                            setTimeout(function () {
                                var response = JSON.parse(jqXHR.responseText);
                                console.log(response);
                                statusBar.css('width', '50%').attr('aria-valuenow', 50);

                                // SET USER DISPLAY AND THEN DISPLAY USER INFO
                                student = response.payload.student;
                                teacher = response.payload.teacher;

                                $("#StudentUsername").html(student.username);
                                $("#StudentPassword").html(student.password);

                                $("#TeacherUsername").html(teacher.username);
                                $("#TeacherPassword").html(teacher.password);
                                $("#UserInfoModal").modal('show');
                            }, time);
                        }
                    });
        }

        function consumeUserInfo() {
            consumedUserInfoGate = true;
            $("#UserInfoModal").modal("hide");
            enrollUsers();
        }

        function enrollUsers() {
            startTime = new Date();
            statusMessage.html("Enrolling users in demo courses...");
            dlap('{{ url('dlap/enroll-users') }}',
                    {
                        parentDomainId: parentDomainId,
                        userspace: registeredUserSpace,
                        studentId: student.id,
                        teacherId: teacher.id,
                        key: '<?= $key ?>'
                    }, function (jqXHR, textStatus) {
                        var time = new Date() - startTime;

                        if (jqXHR.status == 200) {
                            time = time < 1500 ? 1500 - time : 0;

                            setTimeout(function () {
                                var response = JSON.parse(jqXHR.responseText);
                                console.log(response);
                                statusBar.css('width', '85%').attr('aria-valuenow', 85);
                                navigateToNewDomain();
                            }, time);
                        }
                    });
        }

        function navigateToNewDomain() {
            console.log(registeredUserSpace);
            statusMessage.empty().html("Navigating to new domain...");
            setTimeout(function () {
                statusBar.css("width", "100%").attr("aria-valuenow", 100);
                window.location.href = "http://" + registeredUserSpace + ".agilixbuzz.com";
            }, 1500);
        }

        function dlap(uri, _data, func) {
            $.ajax({
                url: uri,
                method: "POST",
                data: JSON.stringify(_data)
            }).complete(function (jqXHR, textStatus) {
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