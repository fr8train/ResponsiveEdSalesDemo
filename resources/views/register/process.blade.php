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
        var currentProgress = 0;
        var enrollmentResponseAjax = {};

        var courseDictionary = {};

        $(document).on('updateProgressBar', progressBarUpdate);

        function progressBarUpdate(e) {
            currentProgress += e.increase;
            $("#statusBar").css('width', currentProgress + '%')
                .attr('aria-valuenow', currentProgress);
        }

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

                                $.event.trigger({
                                    type: 'updateProgressBar',
                                    increase: 2
                                });

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
                                $.event.trigger({
                                    type: 'updateProgressBar',
                                    increase: 3
                                });

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
            statusMessage.html("Creating courses...");

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
                            var response = Boolean(jqXHR.responseJSON) ? jqXHR.responseJSON : JSON.parse(jqXHR.responseText);
                            console.log(response);

                            var hasValidTeacherResponse = Boolean(response.payload && response.payload.toBeEnrolled && response.payload.toBeEnrolled.teacher),
                                hasValidStudentResponse = Boolean(response.payload && response.payload.toBeEnrolled && response.payload.toBeEnrolled.student);

                            if (hasValidStudentResponse) {
                                for (courseId in response.payload.toBeEnrolled.student.enrollments) {
                                    if (!courseDictionary.hasOwnProperty(courseId)) {
                                        courseDictionary[courseId] = 0;
                                    }
                                }
                            }

                            if (hasValidTeacherResponse) {
                                for (courseId in response.payload.toBeEnrolled.teacher.enrollments) {
                                    if (!courseDictionary.hasOwnProperty(courseId)) {
                                        courseDictionary[courseId] = 0;
                                    }
                                }
                            }

                            var perCourseProgressTick = Math.floor((65 / Object.keys(courseDictionary).length) * 100) / 100;
                            var courseDictionaryKeys = Object.keys(courseDictionary);
                            var totalRequests = 0;

                            recursiveCourseCopy(courseDictionaryKeys, courseDictionary, totalRequests, perCourseProgressTick, response);
                        }
                    });
        }

        function recursiveCourseCopy(courseDictionaryKeys, courseDictionary, totalRequests, progressTick, response) {
            var courseIdIdx = courseDictionaryKeys[totalRequests++];

            dlap('{{ url('dlap/derivative-course-copy') }}', {
                userspace: registeredUserSpace,
                parentDomainId: parentDomainId,
                courseId: courseIdIdx,
                key: '<?= $key ?>'
            }, function (jqXHR, textStatus) {
                var response2 = Boolean(jqXHR.responseJSON) ? jqXHR.responseJSON : JSON.parse(jqXHR.responseText);
                console.log(response2);

                courseDictionary[courseIdIdx] = response2.payload[courseIdIdx];

                setTimeout(function () {
                    $.event.trigger({
                        type: 'updateProgressBar',
                        increase: progressTick
                    });

                    if (totalRequests === Object.keys(courseDictionary).length) {
                        prepareEnrollments(response, courseDictionary);
                    } else {
                        recursiveCourseCopy(courseDictionaryKeys, courseDictionary, totalRequests, progressTick, response);
                    }
                }, 200);
            }, false);
        }

        function prepareEnrollments(response, courseDictionary) {
            var totalEnrollments = 0,
                studentTotalEnrollments = 0,
                teacherTotalEnrollments = 0,
                hasValidTeacherResponse = Boolean(response.payload && response.payload.toBeEnrolled && response.payload.toBeEnrolled.teacher),
                hasValidStudentResponse = Boolean(response.payload && response.payload.toBeEnrolled && response.payload.toBeEnrolled.student);


            if (hasValidStudentResponse) {
                studentTotalEnrollments = Object.keys(response.payload.toBeEnrolled.student.enrollments).length;
                totalEnrollments += studentTotalEnrollments;
            }

            if (hasValidTeacherResponse) {
                teacherTotalEnrollments = Object.keys(response.payload.toBeEnrolled.teacher.enrollments).length;
                totalEnrollments += teacherTotalEnrollments;
            }

            var perEnrollmentProgressTick = Math.floor((20 / totalEnrollments) * 100) / 100;

            if (hasValidTeacherResponse && hasValidStudentResponse) {
                for (var courseId in response.payload.toBeEnrolled.student.enrollments) {
                    enrollUser(response.payload.toBeEnrolled.student.id, {
                        courseId: courseDictionary[courseId],
                        rights: response.payload.toBeEnrolled.student.enrollments[courseId]
                    }, perEnrollmentProgressTick);
                }

                for (var courseId in response.payload.toBeEnrolled.teacher.enrollments) {
                    enrollUser(response.payload.toBeEnrolled.teacher.id, {
                        courseId: courseDictionary[courseId],
                        rights: response.payload.toBeEnrolled.teacher.enrollments[courseId]
                    }, perEnrollmentProgressTick);
                }
            }
        }

        function enrollUser(userId, enrollment, perEnrollmentTick) {
            statusMessage.html("Enrolling users in demo courses...");
            var id = uuidv4();
            enrollmentResponseAjax[id] = 1;

            dlap('{{ url('dlap/enroll-user') }}', {
                userId: userId,
                courseId: enrollment.courseId,
                parentDomainId: parentDomainId,
                rights: enrollment.rights,
                key: '<?= $key ?>'
            }, function (jqXHR, textStatus) {
                var response = Boolean(jqXHR.responseJSON) ? jqXHR.responseJSON : JSON.parse(jqXHR.responseText);
                delete enrollmentResponseAjax[id];

                $.event.trigger({
                    type: 'updateProgressBar',
                    increase: perEnrollmentTick
                });

                if (Object.keys(enrollmentResponseAjax).length === 0) {
                    navigateToNewDomain();
                }
            });
        }

        function navigateToNewDomain() {
            console.log(registeredUserSpace);
            statusMessage.empty().html("Navigating to new domain...");
            statusBar.css("width", "100%").attr("aria-valuenow", 100);
            setTimeout(function () {
                window.location.href = "http://" + registeredUserSpace + ".agilixbuzz.com";
            }, 1500);
        }

        function uuidv4() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function dlap(uri, _data, func, async) {
            if (!Boolean(async) && async !== false) {
                async = true;
            }

            $.ajax({
                url: uri,
                method: "POST",
                data: JSON.stringify(_data),
                async: async
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