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
            }
        });

        // FORM SUBMISSION HANDLING
        function formSubmission() {
            var inputs = $('input:visible');
            var warningModalBody = $('#warningModalMessage');
            var containsErrorSwitch = false;

            $.each(inputs,function(index, value){
                var _input = $(value);
                if (!_input.val()) {
                    containsErrorSwitch = true;
                    warningModalBody.append("<h4><i class=\"fa fa-exclamation-circle fa-lg\" style='margin-right: 8px;'></i>" + _input.data('text') + " is missing a value.</h4>");
                }

                if (_input.attr('id') == "email" &&
                        !_input.val().match('\S*?@\S*?\.\S{4}')) {
                    containsErrorSwitch = true;
                    warningModalBody.append("<h4><i class=\"fa fa-exclamation-circle fa-lg\" style='margin-right: 8px;'></i>" + _input.data('text') + " is not a valid " + _input.attr('id') + ".</h4>");
                }
            });

            if (containsErrorSwitch) {
                $('#warningModal').modal('show');
                return false;
            } else {
                return true;
            }
        }

        function searchDomainOfInterest() {
            var _domain = $("#domain:visible");
            var domainName = _domain.val();

            if (domainName) {
                // THERE IS A DOMAIN NAME TO QUERY RETURN BUTTON TO NORMAL
                _domain.parents("div.form-group").removeClass('has-error has-feedback');
                _domain.next().children().first().removeClass('btn-danger');

                $.post('{{ url('dlap/check-domain-availability') }}', {
                    DomainName: domainName,
                    ParentDomainId: '<?= $brand == "brightthinker" ? 27986377 : 27986474 ?>'
                }, function(response){
                    console.log(response);
                },'json');
            } else { // IF DOMAIN NAME IS BLANK
                _domain.parents("div.form-group").addClass('has-error has-feedback');
                _domain.next().children().first().addClass('btn-danger');
            }
        }
    </script>
    @if($brand == "brightthinker")
        <div class="row">
            <!-- PROFESSOR ED -->
            <div class="col-sm-2 hidden-xs">
                <img width="270px" src="{{ url('img/ProfessorEd.png') }}" id="ProfessorEd" alt="Professor Ed Full">
            </div>
            <!-- SUBMISSION FORM -->
            <div class="col-xs-12 col-sm-5">
                <div id="SubmissionFormBox" class="roundedBox">
                    <form id="SubmissionForm" onsubmit="return formSubmission();">
                        <img src="{{ url('img/Logo.png') }}" alt="BrightThinker Logo"
                             class="img-responsive visible-lg visible-xs">
                        <img src="{{ url('img/Logo.png') }}" style="margin-bottom: 17px;" alt="BrightThinker Logo"
                             class="img-responsive visible-md">
                        <img src="{{ url('img/Logo.png') }}" style="margin-bottom: 38px;" alt="BrightThinker Logo"
                             class="img-responsive visible-sm">

                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name" name="firstname">
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
                            <input class="form-control" type="password" id="password" data-text="Password" name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
            </div>
            <!-- TESTIMONIALS -->
            <div class="col-sm-5 hidden-xs">
                <div class="roundedBox testimonialBox">
                    <div class="panel-group" id="accordion" style="margin-bottom: 0px" role="tablist"
                         aria-multiselectable="true">
                        <div class="panel panel-default" style="border: none; background: none; color: #FFF;">
                            <div class="btn btn-lg btn-block submitBtn" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" style="font-weight: 700 !important; font-size: 18px !important;"
                                       data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                       aria-expanded="true" aria-controls="collapseOne">
                                        About Us
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="headingOne">
                                <div class="panel-body" style="max-height: 432px; overflow-y: auto;">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque dui lorem, commodo
                                    suscipit mollis maximus, fermentum non mi. Sed in tincidunt eros, a accumsan diam.
                                    In nunc turpis, imperdiet et elit eget, tempor sagittis nisl. Praesent feugiat est
                                    diam, et finibus elit auctor at. Nullam tellus nibh, maximus ac tempor vitae, auctor
                                    eu elit. Duis at metus auctor, commodo odio sed, malesuada nulla. Vestibulum lectus
                                    nisl, laoreet id ipsum et, vulputate congue nibh. Nulla a ante volutpat, condimentum
                                    metus nec, blandit augue.

                                    Donec magna felis, tincidunt in aliquet vitae, dapibus sodales eros. Fusce faucibus
                                    volutpat lacus, sed imperdiet mi volutpat feugiat. Maecenas dictum neque nec
                                    malesuada suscipit. Proin vestibulum cursus sem ut blandit. Sed ultricies nisl nec
                                    massa fringilla, ullamcorper commodo dui dignissim. Vivamus bibendum elit a placerat
                                    placerat. Nam felis felis, consectetur quis felis ut, sollicitudin viverra felis.

                                    Phasellus facilisis nibh quis tincidunt condimentum. Nulla blandit in lacus eu
                                    tristique. Proin tempus libero at augue suscipit finibus. Nulla sed molestie libero.
                                    Pellentesque efficitur bibendum dui, id egestas tortor dapibus a. Aliquam enim dui,
                                    blandit id ultricies vel, gravida nec ipsum. Vestibulum ante ipsum primis in
                                    faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas maximus placerat
                                    orci nec dictum. Nullam imperdiet, tellus quis commodo scelerisque, risus libero
                                    tristique tellus, ac congue tortor nisi ut lacus.

                                    Cras auctor erat eu molestie laoreet. Vivamus eget risus justo. Aenean bibendum id
                                    libero nec porttitor. Fusce ac pulvinar lorem. Vivamus fringilla fringilla ipsum,
                                    vitae volutpat mi interdum vitae. Suspendisse congue maximus mi, a elementum urna
                                    sodales eu. Aenean interdum felis interdum lectus lobortis imperdiet. Integer ut
                                    odio rutrum, scelerisque sapien molestie, viverra velit. Etiam sollicitudin, ipsum
                                    id vulputate viverra, justo lorem semper purus, at tincidunt magna nisi non ante.
                                    Mauris in mauris purus. Nam suscipit augue ipsum, eget finibus ex fermentum ac.
                                    Aenean semper velit eu risus tristique hendrerit. Pellentesque semper, mauris ac
                                    porta imperdiet, eros urna pretium metus, at ultricies tellus nunc et nunc. Duis id
                                    dui finibus odio placerat tristique.

                                    Pellentesque vestibulum risus quis ex imperdiet, non aliquam magna sagittis. Proin
                                    nec metus at dolor ornare scelerisque congue non lectus. Nullam imperdiet dolor sed
                                    metus lobortis, a malesuada dolor vulputate. Vivamus mattis velit vitae est
                                    fermentum, in tempus elit facilisis. Nunc sed gravida nisi, posuere feugiat diam.
                                    Maecenas id iaculis nunc. Morbi sed varius tortor. Vestibulum eget elementum ligula.
                                    Pellentesque sit amet risus at arcu mattis semper nec porttitor enim. Sed euismod
                                    metus et turpis dictum congue.

                                    Nunc id mauris est. Vestibulum pulvinar arcu sit amet molestie egestas. Duis eu nisl
                                    nunc. Pellentesque diam felis, efficitur non tellus sit amet, mattis facilisis
                                    nulla. Cras viverra ex ut mi facilisis feugiat. Nam lacinia ligula id dolor
                                    facilisis interdum. Proin tincidunt feugiat congue. Fusce id porta dolor. Nam tellus
                                    nibh, luctus eu quam at, pharetra sagittis turpis. Phasellus condimentum arcu sit
                                    amet massa tristique molestie. Phasellus at massa ac velit congue consequat. Donec
                                    rhoncus et urna eget lobortis. Mauris id risus elit. Nam mi orci, malesuada id ex
                                    venenatis, euismod convallis dolor.

                                    Nulla vitae orci a augue imperdiet dignissim a eget urna. Donec tincidunt purus dui,
                                    nec tristique odio dignissim id. In feugiat commodo placerat. Etiam quis ipsum at
                                    quam imperdiet faucibus. Donec vulputate urna vitae sapien sagittis, sit amet
                                    sodales metus blandit. Curabitur aliquet purus eu accumsan commodo. Donec eget massa
                                    ac mauris finibus faucibus non in nisi. Quisque euismod, nulla sed ullamcorper
                                    gravida, mauris nisi ornare erat, vel tristique elit arcu ut ex. Etiam sit amet odio
                                    lacus. Integer ante sem, cursus sit amet eleifend vitae, semper at sem. Praesent
                                    feugiat, ipsum in rhoncus fermentum, quam enim commodo nibh, quis fringilla orci
                                    elit quis risus. Praesent mollis elit vel pellentesque accumsan.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" style="border: none; background: none; color: #FFF;">
                            <div class="btn btn-lg btn-block submitBtn" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed"
                                       style="font-weight: 700 !important; font-size: 18px !important;" role="button"
                                       data-toggle="collapse" data-parent="#accordion"
                                       href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Testimonials
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body" style="max-height: 432px; overflow-y: auto;">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <!-- SUBMISSION FORM -->
            <div id="SubmissionFormBox" class="col-xs-12 col-sm-7 roundedBox" style="padding-left: 0px; padding-right: 0px;">
                <div class="col-md-5 hidden-xs hidden-sm">
                    <img src="{{ url('img/knowledge_U_vert.png') }}" alt="KnowledgeU Logo"
                         class="img-responsive" style="margin-top: 157px;margin-bottom: 157px;">
                </div>
                <div class="col-xs-12 visible-xs visible-sm">
                    <img src="{{ url('img/knowledge_U_horiz.png') }}" alt="KnowledgeU Logo"
                         class="img-responsive">
                </div>
                <div class="col-xs-12 visible-xs visible-sm">
                    <form id="SubmissionForm" onsubmit="return formSubmission();" style="margin-bottom: 8px;">
                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name" name="firstname">
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
                            <input class="form-control" type="password" id="password" data-text="Password" name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
                <div class="hidden-xs hidden-sm col-md-7">
                    <form id="SubmissionForm" onsubmit="return formSubmission();" style="margin-top: 50px;margin-bottom: 50px;">
                        <div class="form-group">
                            <label for="firstname">First name</label>
                            <input class="form-control" type="text" id="firstname" data-text="First Name" name="firstname">
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
                            <input class="form-control" type="password" id="password" data-text="Password" name="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="domain">Domain name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="domain" data-text="Domain of Interest"
                                       name="domain">
                                <span class="input-group-btn">
                                    <button type="button" onclick="searchDomainOfInterest();" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-block submitBtn">Register</button>
                    </form>
                </div>
            </div>
            <!-- TESTIMONIALS -->
            <div class="col-sm-5 hidden-xs">
                <div class="roundedBox testimonialBox">
                    <div class="panel-group" id="accordion" style="margin-bottom: 0px" role="tablist"
                         aria-multiselectable="true">
                        <div class="panel panel-default" style="border: none; background: none; color: #FFF;">
                            <div class="btn btn-lg btn-block submitBtn" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" style="font-weight: 700 !important; font-size: 18px !important;"
                                       data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                       aria-expanded="true" aria-controls="collapseOne">
                                        About Us
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="headingOne">
                                <div class="panel-body" style="max-height: 432px; overflow-y: auto;">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque dui lorem, commodo
                                    suscipit mollis maximus, fermentum non mi. Sed in tincidunt eros, a accumsan diam.
                                    In nunc turpis, imperdiet et elit eget, tempor sagittis nisl. Praesent feugiat est
                                    diam, et finibus elit auctor at. Nullam tellus nibh, maximus ac tempor vitae, auctor
                                    eu elit. Duis at metus auctor, commodo odio sed, malesuada nulla. Vestibulum lectus
                                    nisl, laoreet id ipsum et, vulputate congue nibh. Nulla a ante volutpat, condimentum
                                    metus nec, blandit augue.

                                    Donec magna felis, tincidunt in aliquet vitae, dapibus sodales eros. Fusce faucibus
                                    volutpat lacus, sed imperdiet mi volutpat feugiat. Maecenas dictum neque nec
                                    malesuada suscipit. Proin vestibulum cursus sem ut blandit. Sed ultricies nisl nec
                                    massa fringilla, ullamcorper commodo dui dignissim. Vivamus bibendum elit a placerat
                                    placerat. Nam felis felis, consectetur quis felis ut, sollicitudin viverra felis.

                                    Phasellus facilisis nibh quis tincidunt condimentum. Nulla blandit in lacus eu
                                    tristique. Proin tempus libero at augue suscipit finibus. Nulla sed molestie libero.
                                    Pellentesque efficitur bibendum dui, id egestas tortor dapibus a. Aliquam enim dui,
                                    blandit id ultricies vel, gravida nec ipsum. Vestibulum ante ipsum primis in
                                    faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas maximus placerat
                                    orci nec dictum. Nullam imperdiet, tellus quis commodo scelerisque, risus libero
                                    tristique tellus, ac congue tortor nisi ut lacus.

                                    Cras auctor erat eu molestie laoreet. Vivamus eget risus justo. Aenean bibendum id
                                    libero nec porttitor. Fusce ac pulvinar lorem. Vivamus fringilla fringilla ipsum,
                                    vitae volutpat mi interdum vitae. Suspendisse congue maximus mi, a elementum urna
                                    sodales eu. Aenean interdum felis interdum lectus lobortis imperdiet. Integer ut
                                    odio rutrum, scelerisque sapien molestie, viverra velit. Etiam sollicitudin, ipsum
                                    id vulputate viverra, justo lorem semper purus, at tincidunt magna nisi non ante.
                                    Mauris in mauris purus. Nam suscipit augue ipsum, eget finibus ex fermentum ac.
                                    Aenean semper velit eu risus tristique hendrerit. Pellentesque semper, mauris ac
                                    porta imperdiet, eros urna pretium metus, at ultricies tellus nunc et nunc. Duis id
                                    dui finibus odio placerat tristique.

                                    Pellentesque vestibulum risus quis ex imperdiet, non aliquam magna sagittis. Proin
                                    nec metus at dolor ornare scelerisque congue non lectus. Nullam imperdiet dolor sed
                                    metus lobortis, a malesuada dolor vulputate. Vivamus mattis velit vitae est
                                    fermentum, in tempus elit facilisis. Nunc sed gravida nisi, posuere feugiat diam.
                                    Maecenas id iaculis nunc. Morbi sed varius tortor. Vestibulum eget elementum ligula.
                                    Pellentesque sit amet risus at arcu mattis semper nec porttitor enim. Sed euismod
                                    metus et turpis dictum congue.

                                    Nunc id mauris est. Vestibulum pulvinar arcu sit amet molestie egestas. Duis eu nisl
                                    nunc. Pellentesque diam felis, efficitur non tellus sit amet, mattis facilisis
                                    nulla. Cras viverra ex ut mi facilisis feugiat. Nam lacinia ligula id dolor
                                    facilisis interdum. Proin tincidunt feugiat congue. Fusce id porta dolor. Nam tellus
                                    nibh, luctus eu quam at, pharetra sagittis turpis. Phasellus condimentum arcu sit
                                    amet massa tristique molestie. Phasellus at massa ac velit congue consequat. Donec
                                    rhoncus et urna eget lobortis. Mauris id risus elit. Nam mi orci, malesuada id ex
                                    venenatis, euismod convallis dolor.

                                    Nulla vitae orci a augue imperdiet dignissim a eget urna. Donec tincidunt purus dui,
                                    nec tristique odio dignissim id. In feugiat commodo placerat. Etiam quis ipsum at
                                    quam imperdiet faucibus. Donec vulputate urna vitae sapien sagittis, sit amet
                                    sodales metus blandit. Curabitur aliquet purus eu accumsan commodo. Donec eget massa
                                    ac mauris finibus faucibus non in nisi. Quisque euismod, nulla sed ullamcorper
                                    gravida, mauris nisi ornare erat, vel tristique elit arcu ut ex. Etiam sit amet odio
                                    lacus. Integer ante sem, cursus sit amet eleifend vitae, semper at sem. Praesent
                                    feugiat, ipsum in rhoncus fermentum, quam enim commodo nibh, quis fringilla orci
                                    elit quis risus. Praesent mollis elit vel pellentesque accumsan.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" style="border: none; background: none; color: #FFF;">
                            <div class="btn btn-lg btn-block submitBtn" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed"
                                       style="font-weight: 700 !important; font-size: 18px !important;" role="button"
                                       data-toggle="collapse" data-parent="#accordion"
                                       href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Testimonials
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body" style="max-height: 432px; overflow-y: auto;">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"><strong style="margin-right: 5px;">WARNING</strong><small>Validation errors detected</small></h3>
                </div>
                <div class="modal-body" id="warningModalMessage">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
        }, jQuery);
    </script>
@stop