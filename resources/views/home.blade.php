@extends('templates.min')

@section('title','Home')

@section('body')
    <nav class="navbar navbar-default navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Admin Panel</a>
            </div>
            <div class="collapse navbar-collapse" id="user-navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><img alt="person-icon" style="margin-top: 10px;" class="pull-left" width="30"
                             src="{{ url('img/person.png') }}"></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">{{ "{$user->firstname} {$user->lastname}" }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('auth/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div id="body" class="col-xs-12">
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var _body;
        $(document).ready(function () {
            _body = $("#body");

            // INITIALIZATION
            getAllDomains();
        });

        function getAllDomains(btFilter, kuFilter) {
            _body.empty();
            $.getJSON("{{ url('dlap/all-domains') }}")
                    .done(function (data) {
                        console.log(data);
                        _body.append($('<div class="col-xs-12"><h1>Domains<button type="button" onclick="getAllDomains()" class="btn btn-lg btn-default" style="margin-left: 10px;"><i class="fa fa-refresh"></i></button></h1></div>'));

                        var btDomains = $('<div></div>')
                                .attr('class', 'col-sm-6')
                                .append($('<h3><a target="_blank" href="{{ url('register/bright-thinker') }}"><img height="32" alt="BTLogo" src="{{ url('img/ProfessorEd-headshot.png') }}"></a>BrightThinker Domains</h3>'))
                                .append('<div class="row" style="margin-bottom: 10px;"><div class="col-xs-12"><div class="input-group"><input id="btFilterInput" type="text" class="form-control" placeholder="Filter by..."><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="filterResults(\'btFilterInput\')"><i class="fa fa-filter"></i></button></span></div></div></div>');

                        $.each(data.payload.btdemo, function (k, v) {
                            if (btFilter) {
                                var re = new RegExp(btFilter, 'i');
                                if (v.name.match(re)
                                        || v.reference.match(re)
                                        || v.userspace.match(re))
                                    btDomains.append(createDomainDisplayCard(v));
                            } else
                                btDomains.append(createDomainDisplayCard(v));
                        });

                        var kuDomains = $('<div></div>')
                                .attr('class', 'col-sm-6')
                                .append($('<h3><a target="_blank" href="{{ url('register/knowledge-u') }}"><img height="32" alt="BTLogo" src="{{ url('img/KnowledgeUBox.png') }}"></a>KnowledgeU Domains</h3>'))
                                .append('<div class="row" style="margin-bottom: 10px;"><div class="col-xs-12"><div class="input-group"><input id="kuFilterInput" type="text" class="form-control" placeholder="Filter by..."><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="filterResults(\'kuFilterInput\')"><i class="fa fa-filter"></i></button></span></div></div></div>');

                        $.each(data.payload.kudemo, function (k, v) {
                            if (kuFilter) {
                                var re = new RegExp(kuFilter, 'i');
                                if (v.name.match(re)
                                        || v.reference.match(re)
                                        || v.userspace.match(re))
                                    kuDomains.append(createDomainDisplayCard(v));
                            } else
                                kuDomains.append(createDomainDisplayCard(v));
                        });

                        _body.append(btDomains).append(kuDomains);
                    })
                    .fail(function (jqxhr, textStatus, error) {
                        console.log(error);
                    });
        }

        function filterResults(id) {
            if (id.match(/bt/))
                getAllDomains($("#" + id).val());
            else
                getAllDomains(null, $("#" + id).val());
        }

        function createDomainDisplayCard(domain) {
            var diff = dateDiff(new Date(domain.creationdate), new Date());
            var _diff = $('<dd></dd>')
                    .append(diff);

            if (diff >= 30)
                _diff.append($('<span class="label label-danger" style="margin-left: 8px;">EXPIRED</span>'));

            var uri = domain.userspace + ".agilixbuzz.com";

            var _dl = $('<dl></dl>')
                    .attr('class', 'dl-horizontal')
                    .append('<dt>Live URL</dt>')
                    .append('<dd><a target="_blank" href="http://' + uri + '">' + uri + '</a></dd>')
                    .append('<dt>Days active</dt>')
                    .append(_diff);

            var btnGroup = $('<div class="btn-group btn-group-sm pull-right" role="group"></div>')
                    .append($('<button onclick="editDomain(' + domain.id + ')"></button>')
                            .attr({
                                class: 'btn btn-default',
                                type: 'button',
                                title: 'Edit'
                            })
                            .html('<i class="fa fa-edit"></i>'))
                    .append($('<button onclick="convertDomain(' + domain.id + ')"></button>')
                            .attr({
                                class: 'btn btn-default',
                                type: 'button',
                                title: 'Convert'
                            })
                            .html('<i class="fa fa-random"></i>'))
                    .append($('<button onclick="deleteDomain(' + domain.id + ')"></button>')
                            .attr({
                                class: 'btn btn-default',
                                type: 'button',
                                title: 'Delete'
                            })
                            .html('<i class="fa fa-trash"></i>'));

            return $('<div class="panel panel-primary"></div>')
                    .css('position', 'relative')
                    .append($('<div class="panel-heading"></div>')
                            .append($('<h3 class="panel-title"></h3>')
                                    .append(domain.name)
                                    .append($('<span class="label label-info pull-right"></span>')
                                            .html(domain.id))))
                    .append($('<div class="panel-body"></div>')
                            .append(_dl)
                            .append(btnGroup));
        }

        function dateDiff(date1, date2) {
            return Math.floor((date2 - date1) / (1000 * 60 * 60 * 24));
        }

        function editDomain(id) {
            $.getJSON("{{ url('dlap/domain') }}/" + id)
                    .done(function (data) {
                        console.log(data);
                        buildOutEditScreen(data.payload.domain, data.payload.users);
                    })
                    .fail(function (jqxhr, textStatus, error) {
                        console.log(error);
                    });
        }

        function buildOutEditScreen(domain, users) {
            _body.empty();
            _body.append('<div class="col-xs-12" style="margin-bottom: 15px;"><a target="_self" href="">Return back to Domains</a></div>');

            var creationdate = new Date(domain.creationdate);
            var modifieddate = new Date(domain.modifieddate);

            var panelForm = $('<form class="form-horizontal"></form>')
                    .append(createFormGroupReadOnly('ID', 'id', domain.id))
                    .append(createFormGroup('Domain name', 'name', domain.name))
                    .append(createFormGroupReadOnly('Domain space', 'userspace-ro', domain.userspace))
                    .append(createFormGroup('Reference', 'reference', domain.reference))
                    .append(createFormGroupReadOnly('Created by', 'creationby-ro', domain.creationby))
                    .append(createFormGroupReadOnly('Created', 'created-ro', creationdate.toLocaleString()))
                    .append(createFormGroupReadOnly('Modified by', 'modifiedby-ro', domain.modifiedby))
                    .append(createFormGroupReadOnly('Modified', 'modified-ro', modifieddate.toLocaleString()))
                    .append('<div class="row"><div class="col-xs-12"><button type="button" class="btn btn-primary pull-right" onclick="saveDomainInfo(' + domain.id + ')">Save Domain Info</button></div></div>')
                    .append('<h4>Users</h4><hr/>');

            $.each(users, function (k, v) {
                var lastlogindate = new Date(v.lastlogindate);
                panelForm.append(createFormGroupReadOnly('ID', 'id-' + v.id, v.id))
                        .append(createFormGroup('First name', 'firstname-' + v.id, v.firstname))
                        .append(createFormGroup('Last name', 'lastname-' + v.id, v.lastname))
                        .append(createFormGroup('Email', 'email-' + v.id, v.email))
                        .append(createFormGroup('Username', 'username-' + v.id, v.username))
                        .append(createFormGroup('Reference', 'reference-' + v.id, v.reference));

                if (k == users.length - 1)
                    panelForm.append(createFormGroupReadOnly('Last login', 'lastlogindate-' + v.id + '-ro', lastlogindate.toLocaleString()));
                else
                    panelForm.append($(createFormGroupReadOnly('Last login', 'lastlogindate-' + v.id + '-ro', lastlogindate.toLocaleString())).css('margin-bottom', '49px'));
            });

            panelForm.append('<div class="row"><div class="col-xs-12"><button type="button" class="btn btn-primary pull-right" onclick="saveUserInfo(' + domain.id + ')">Save User Info</button></div></div>');

            var panelBody = $('<div class="row"></div>')
                    .append($('<div class="col-xs-12"></div>')
                            .append(panelForm));

            var panel = $('<div class="panel panel-primary"></div>')
                    .append($('<div class="panel-heading"></div>')
                            .append($('<h3 class="panel-title">Edit Domain</h3>')))
                    .append($('<div class="panel-body"></div>')
                            .append(panelBody));

            _body.append($('<div class="col-xs-12 col-sm-8"></div>')
                    .append(panel));
        }

        function saveDomainInfo(id) {
            var domain = {};

            var notDomainField = new RegExp(".*?\-.*?");
            $('input').each(function (k, v) {
                if (!notDomainField.test(v.id)) {
                    domain[v.id] = $(v).val();
                }
            });

            console.log(domain);

            $.post('{{ url('dlap/domain') }}', JSON.stringify(domain), function (data, textStatus) {
                console.log(data);

                editDomain(id)
            }, "json");
        }

        function saveUserInfo(domainId) {
            var users = {};

            var userField = new RegExp(".*?\-.*?");
            var endsWith = new RegExp("\-ro$");

            $('input').each(function (k, v) {
                if (userField.test(v.id) && !endsWith.test(v.id)) {
                    idSplit = v.id.split("-");

                    if (!users[idSplit[1]])
                        users[idSplit[1]] = {};

                    users[idSplit[1]][idSplit[0]] = $(v).val();
                }
            });

            console.log(users);

            $.post('{{ url('dlap/users') }}', JSON.stringify(users), function (data, textStatus) {
                console.log(data);

                editDomain(domainId);
            });
        }

        function createFormGroup(label, name, value) {
            return $('<div class="form-group"></div>')
                    .append($('<label></label>')
                            .attr({
                                for: name,
                                class: 'col-sm-3 control-label'
                            })
                            .html(label))
                    .append($('<div class="col-sm-9"></div>')
                            .append($('<input />')
                                    .attr({
                                        type: 'text',
                                        class: 'form-control',
                                        id: name,
                                        name: name,
                                        value: value
                                    })));
        }

        function createFormGroupReadOnly(label, name, value) {
            return $('<div class="form-group"></div>')
                    .append($('<label></label>')
                            .attr({
                                for: name,
                                class: 'col-sm-3 control-label'
                            })
                            .html(label))
                    .append($('<div class="col-sm-9"></div>')
                            .append($('<input />')
                                    .attr({
                                        type: 'text',
                                        class: 'form-control',
                                        id: name,
                                        name: name,
                                        value: value,
                                        disabled: 'disabled'
                                    })));
        }

        function deleteDomain(id) {
            if (confirm("Are you sure you want to delete domain (ID=" + id + ")?")) {
                $.post('{{ url('dlap/domain') }}', JSON.stringify({
                    id: id,
                    deleteDomain: 1
                }), function (data, textStatus) {
                    console.log(data);

                    getAllDomains();
                }, "json");
            }
        }

        function convertDomain(id) {
            if (confirm("Are you sure you want to convert domain (ID=" + id + ")?")) {
                $.post('{{ url('dlap/domain') }}', JSON.stringify({
                    id: id,
                    parentid: 12444139
                }), function (data, textStatus) {
                    console.log(data);

                    getAllDomains();
                }, "json");
            }
        }
    </script>
@endsection