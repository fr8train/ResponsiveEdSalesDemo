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
    <?php
    $brightThinker = array();
    $brightThinker['aboutUs'] = "<p>BrightThinker is at the forefront of the new educational model–“Blended Education.” This dynamic, comprehensive, web-delivered eCurriculum is designed for the 3rd-5th grades. As today’s students have changed, so have their educational expectations and academic needs. BrightThinker combines rigorous content, rich multimedia, and the Internet to generate academic discovery, so students receive a real-world online education experience.<p>
<h4 class='custom-header'>RIGOROUS ACADEMICS</h4><hr class='custom-divider'><p>Through rigorous academics, students are prepared for success as measured by standardized assessments and collegiate preparedness.</p>
<h4 class='custom-header'>EXCEEDS STANDARDS</h4><hr class='custom-divider'><p>The development team has developed educational products that adhere to state and national standards in both architecture and content.</p>
<h4 class='custom-header'>ONLINE EDUCATIONAL MODEL</h4><hr class='custom-divider'><p>The online classroom model better prepares students for academic success at the next level in the growing online college environment.</p>";

    $brightThinker['testimonials'] = "<blockquote style='border-left: none;'>
  <p>“The COMPREHEND curriculum has helped my teachers and students.  Even our SpEd students have experienced success due to the fun, multi-media approach.  Our students enjoy the online learning and that’s the biggest plus.”</p>
  <footer>Ms. Rogeness, Associate Director of Texas Virtual Academy</footer>
</blockquote>
<blockquote style='border-left: none;'>
  <p>“The students are enjoying the COMPREHEND curriculum, and my educators appreciate the flexibility!  Our students love the ability to proceed based on their ability.  We know that this approach is better preparing them for college.”</p>
  <footer>Mr. Griffin, Director of Premier Charter School of Lubbock</footer>
</blockquote>";
    $brightThinker['tryFree'] = "<h4 class='text-center'>Then let’s get started!</h4>
<p>Just complete the registration form on this page by filling in the blanks to create your personal login and password.</p>
<p>Then enter your school’s name or school domain name in the “Domain Name” field.</p>
<p>Now click the magnifying glass to see if it is available. If it turns <span style='color:#d9534f; font-weight: bold;'>RED</span>, try another name.  If it turns <span style='color:#5cb85c; font-weight: bold;'>GREEN</span>, you are on your way.</p>
<br>
<h4 class='text-center'>For the next 30 days, have fun and let us know what you think!</h4>";

    $knowledgeU = array();
    $knowledgeU['aboutUs'] = "<p>KnowledgeU is at the forefront of the new educational model–“Blended Education.” This dynamic, comprehensive, web-delivered eCurriculum is designed for the 6th-12th grades. As today’s students have changed, so have their educational expectations and academic needs. KnowledgeU combines rigorous content, rich multimedia, and the Internet to generate academic discovery, so students receive a real-world online education experience.<p>
<h4 class='custom-header'>RIGOROUS ACADEMICS</h4><hr class='custom-divider'><p>Through rigorous academics, students are prepared for success as measured by standardized assessments and collegiate preparedness.</p>
<h4 class='custom-header'>EXCEEDS STANDARDS</h4><hr class='custom-divider'><p>The development team has developed educational products that adhere to state and national standards in both architecture and content.</p>
<h4 class='custom-header'>ONLINE EDUCATIONAL MODEL</h4><hr class='custom-divider'><p>The online classroom model better prepares students for academic success at the next level in the growing online college environment.</p>";

    $knowledgeU['testimonials'] = $brightThinker['testimonials'];
    $knowledgeU['tryFree'] = "<h4 class='text-center'>Then let’s get started!</h4>
<p>Just complete the registration form on this page by filling in the blanks to create your personal login and password.</p>
<p>Then enter your school’s name or school domain name in the “Domain Name” field.</p>
<p>Now click the magnifying glass to see if it is available. If it turns <span style='color:#d9534f; font-weight: bold;'>RED</span>, try another name.  If it turns <span style='color:#5cb85c; font-weight: bold;'>GREEN</span>, you are on your way.</p>
<br>
<h4 class='text-center'>For the next 30 days, have fun and let us know what you think!</h4>";
    ?>
    <style type="text/css">
        .custom-header {
            margin: 20px 0px 5px;
        }

        .custom-divider {
            margin: 0px 0px 5px;
        }

        .content-section {
            overflow-y: auto;
            padding-top: 15px;
            height: 380px;
            max-height: 380px;
            color: white;
        }

        blockquote footer {
            color: lightgrey;
        }
    </style>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        });

        // FORM SUBMISSION HANDLING
        function formSubmission() {
            var inputs = $('input:visible');
            var warningModalBody = $('#warningModalMessage');
            warningModalBody.empty();

            var containsErrorSwitch = false;

            $.each(inputs, function (index, value) {
                var _input = $(value);
                var _message = "";
                if (!_input.val()) {
                    containsErrorSwitch = true;
                    _message = _input.data('text') + " is missing a value.";
                }

                if (_input.attr('id') == "email" && !_input.val().match(/\S*?@\S*?\.\S{2,4}/)) {
                    containsErrorSwitch = true;
                    _message = _input.data('text') + " is not a valid " + _input.attr('id');
                }

                if (_input.attr('id') == "domain" && !_input.parent().parent().hasClass('has-success')) {
                    _message = "You need to verify the domain you've picked.<br/>Please click on the search button (magnifying glass).";
                    if (_input.parent().parent().hasClass('has-error'))
                        _message = _input.data('text') + " is not an available " + _input.attr('id');
                    containsErrorSwitch = true;
                }

                var newRow = $("<tr></tr>")
                        .addClass('text-danger')
                        .append(
                        $("<td></td>")
                                .attr('valign', 'top')
                                .html($("<i></i>")
                                        .attr('class', 'fa fa-exclamation-circle fa-lg center-block')))
                        .append(
                        $("<td></td>")
                                .html(_message));
                warningModalBody.append(newRow);
            });

            if (containsErrorSwitch) {
                $('#warningModal').modal('show');
                return false;
            } else {
                return true;
            }
        }

        function searchDomainOfInterest() {
            var _domain = $('[id="domain"]:visible');
            var domainName = _domain.val();

            if (domainName) {
                // THERE IS A DOMAIN NAME TO QUERY RETURN BUTTON TO NORMAL
                _domain.parents("div.form-group").removeClass('has-error has-success has-feedback');
                _domain.next().children().first().removeClass('btn-danger btn-success');
                $('[id="domainNotAvailable"]:visible').addClass('hide');

                var _data = JSON.stringify({
                    domainName: domainName,
                    parentDomainId: '<?= $brand == "brightthinker" ? 27986377 : 27986474 ?>'
                });

                $.ajax({
                    url: '{{ url('dlap/check-domain-availability') }}',
                    method: "POST",
                    data: _data
                }).complete(function (jqXHR, textStatus) {
                    console.log(textStatus);
                    console.log(jqXHR);

                    if (textStatus == "error") {
                        _domain.parents("div.form-group").addClass('has-error has-feedback');
                        _domain.next().children().first().addClass('btn-danger');
                        _domain.parent().next().removeClass('hide');
                    } else if (textStatus == "success") {
                        _domain.parents("div.form-group").addClass('has-success has-feedback');
                        _domain.next().children().first().addClass('btn-success');
                    } else {
                        alert(textStatus);
                    }
                });
            } else { // IF DOMAIN NAME IS BLANK
                _domain.parents("div.form-group").addClass('has-error has-feedback');
                _domain.next().children().first().addClass('btn-danger');
                _domain.parent().next().removeClass('hide');
            }
        }
    </script>
    @if($brand == "brightthinker")
        <div id="content" class="row">
            <!-- PROFESSOR ED -->
            <div class="col-sm-2 hidden-xs">
                <img width="270px" src="{{ url('img/ProfessorEd.png') }}" id="ProfessorEd" alt="Professor Ed Full">
            </div>
            <!-- SUBMISSION FORM -->
            <div class="col-xs-12 col-sm-5">
                <div id="SubmissionFormBox" class="roundedBox">
                    <form id="SubmissionForm" action="{{ Request::url() }}" method="post"
                          onsubmit="return formSubmission();">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <img src="{{ url('img/Logo.png') }}" alt="BrightThinker Logo"
                             class="img-responsive visible-lg visible-xs">
                        <img src="{{ url('img/Logo.png') }}" style="margin-bottom: 17px;" alt="BrightThinker Logo"
                             class="img-responsive visible-md">
                        <img src="{{ url('img/Logo.png') }}" style="margin-bottom: 38px;" alt="BrightThinker Logo"
                             class="img-responsive visible-sm">

                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name"
                                   name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last name</label>
                            <input class="form-control" type="text" id="lastname" data-text="Last Name" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" id="email" data-text="Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" id="password" data-text="Password"
                                   name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>

                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i
                                                class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <span id="domainNotAvailable" class="help-block hide">Domain is not available - please try another.</span>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
            </div>
            <!-- TESTIMONIALS -->
            <div class="col-sm-5 hidden-xs">
                <div class="roundedBox testimonialBox">
                    <button type="button" id="brightThinkerAboutUsBtn" class="btn submitBtn btn-lg btn-block">About Us
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse in" id="brightThinkerAboutUs">
                                <?= $brightThinker['aboutUs'] ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="brightThinkerTestimonialsBtn" class="btn submitBtn btn-lg btn-block">
                        Testimonials
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse" id="brightThinkerTestimonials">
                                <?= $brightThinker['testimonials'] ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="brightThinkerTestDriveBtn" class="btn submitBtn btn-lg btn-block">Test drive
                        for free?
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse" id="brightThinkerTestDrive">
                                <?= $brightThinker['tryFree'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div id="content" class="row">
            <!-- SUBMISSION FORM -->
            <div id="SubmissionFormBox" class="col-xs-12 col-sm-7 roundedBox"
                 style="padding-left: 0px; padding-right: 0px;">
                <div class="col-md-5 hidden-xs hidden-sm">
                    <img src="{{ url('img/knowledge_U_vert.png') }}" alt="KnowledgeU Logo"
                         class="img-responsive" style="margin-top: 157px;margin-bottom: 157px;">
                </div>
                <div class="col-xs-12 visible-xs visible-sm">
                    <img src="{{ url('img/knowledge_U_horiz.png') }}" alt="KnowledgeU Logo"
                         class="img-responsive">
                </div>
                <div class="col-xs-12 visible-xs visible-sm">
                    <form id="SubmissionForm" action="{{ Request::url() }}" method="post"
                          onsubmit="return formSubmission();" style="margin-bottom: 8px;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name"
                                   name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last name</label>
                            <input class="form-control" type="text" id="lastname" data-text="Last Name" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" id="email" data-text="Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" id="password" data-text="Password"
                                   name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>

                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i
                                                class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <span id="domainNotAvailable" class="help-block hide">Domain is not available - please try another.</span>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
                <div class="hidden-xs hidden-sm col-md-7">
                    <form id="SubmissionForm" action="{{ Request::url() }}" method="post"
                          onsubmit="return formSubmission();"
                          style="margin-top: 50px;margin-bottom: 50px;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name"
                                   name="firstname">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last name</label>
                            <input class="form-control" type="text" id="lastname" data-text="Last Name" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" id="email" data-text="Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" id="password" data-text="Password"
                                   name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>

                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i
                                                class="fa fa-search"></i></button>
                                </span>
                            </div>
                            <span id="domainNotAvailable" class="help-block hide">Domain is not available - please try another.</span>
                        </div>
                        <button type="submit" class="btn submitBtn btn-lg btn-block">Register</button>
                    </form>
                </div>
            </div>
            <!-- TESTIMONIALS -->
            <div class="col-sm-5 hidden-xs">
                <div class="roundedBox testimonialBox">
                    <button type="button" id="KnowledgeUAboutUsBtn" class="btn submitBtn btn-lg btn-block">About Us
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse in" id="KnowledgeUAboutUs">
                                <?= $knowledgeU['aboutUs'] ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="KnowledgeUTestimonialsBtn" class="btn submitBtn btn-lg btn-block">
                        Testimonials
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse" id="KnowledgeUTestimonials">
                                <?= $knowledgeU['testimonials'] ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="KnowledgeUTestDriveBtn" class="btn submitBtn btn-lg btn-block">Test drive
                        for free?
                    </button>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-section collapse" id="KnowledgeUTestDrive">
                                <?= $knowledgeU['tryFree'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

                <!-- WARNING MODAL -->
        <div id="warningModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title"><strong style="margin-right: 5px;">WARNING</strong><br
                                    class="visible-xs"/>
                            <small>Validation errors detected</small>
                        </h3>
                    </div>
                    <div class="modal-body">
                        <table>
                            <thead>
                            <tr>
                                <th width="25" style="width: 25px;"></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="warningModalMessage">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div><!-- /.modal -->
@stop

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            var targetSection;

            // OBJECT CENTERING
            centerObjects();

            $(window).on('resize', function () {
                centerObjects();
            })

            $(".collapse").on('hidden.bs.collapse', function () {
                targetSection.collapse('show');
            });

            function centerObjects() {
                var _window = $(window);
                var _box = $('#content');
                var _marginTop;

                if (_box.height() >= _window.height())
                    _marginTop = 0;
                else
                    _marginTop = Math.floor((_window.height() - _box.height()) / 2);

                _box.css('margin-top', _marginTop + 'px');
            }

            // CUSTOM ACCORDIAN
            $("#KnowledgeUTestDriveBtn, #KnowledgeUTestimonialsBtn, #KnowledgeUAboutUsBtn, #brightThinkerAboutUsBtn, #brightThinkerTestDriveBtn, #brightThinkerTestimonialsBtn").click(function () {
                var _this = $(this);
                targetSection = $("#" + _this.attr('id').split('Btn')[0]);
                //console.log("#" + _this.attr('id').split('Btn')[0]);

                if (!targetSection.hasClass('in')) {
                    $(".collapse.in").collapse('hide');
                }
            });
        }, jQuery);
    </script>
@stop