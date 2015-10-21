@extends('templates.min')

@section('title','Home')

@section('body')
    <nav class="navbar navbar-default navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Admin Panel</a>
            </div>
            <div class="navbar-right">
                <img alt="person-icon" style="margin-top: 10px;" class="pull-left" width="30"
                     src="{{ url('img/person.png') }}">

                <p class="navbar-text">{{ "{$user->firstname} {$user->lastname}" }}</p>
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

        function getAllDomains() {
            _body.empty();
            $.getJSON("{{ url('dlap/all-domains') }}")
                    .done(function (data) {
                        console.log(data);
                        _body.append($('<div class="col-xs-12"><h1>Domains<button type="button" onclick="getAllDomains()" class="btn btn-lg btn-default" style="margin-left: 10px;"><i class="fa fa-refresh"></i></button></h1></div>'));

                        var btDomains = $('<div></div>')
                                .attr('class', 'col-sm-6')
                                .append($('<h3><img height="32" alt="BTLogo" src="{{ url('img/ProfessorEd-headshot.png') }}">BrightThinker Domains</h3>'));

                        $.each(data.payload.btdemo, function (k, v) {
                            btDomains.append(createDomainDisplayCard(v));
                        });

                        var kuDomains = $('<div></div>')
                                .attr('class', 'col-sm-6')
                                .append($('<h3><img height="32" alt="BTLogo" src="{{ url('img/KnowledgeUBox.png') }}">KnowledgeU Domains</h3>'));

                        $.each(data.payload.kudemo, function (k, v) {
                            kuDomains.append(createDomainDisplayCard(v));
                        });

                        _body.append(btDomains).append(kuDomains);
                    })
                    .fail(function (jqxhr, textStatus, error) {
                        console.log(error);
                    });
        }

        function createDomainDisplayCard(domain) {
            var diff = dateDiff(new Date(domain.creationdate), new Date());
            var _diff = $('<dd></dd>')
                    .append(diff);

            if (diff >= 30)
                _diff.append($('<span class="label label-danger" style="margin-left: 8px;">EXPIRED</span>'));

            var uri = "http://" + domain.userspace + ".agilixbuzz.com";

            var _dl = $('<dl></dl>')
                    .attr('class', 'dl-horizontal')
                    .append('<dt>Live URL</dt>')
                    .append('<dd><a target="_blank" href="' + uri + '">' + uri + '</a></dd>')
                    .append('<dt>Days active</dt>')
                    .append(_diff);

            var btnGroup = $('<div class="btn-group btn-group-sm pull-right" role="group"></div>')
                    .css({
                        position: 'absolute',
                        bottom: '10px',
                        right: '10px'
                    })
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
                    .append(btnGroup)
                    .append($('<div class="panel-heading"></div>')
                            .append($('<h3 class="panel-title"></h3>')
                                    .append(domain.name)
                                    .append($('<span class="label label-info pull-right"></span>')
                                            .html(domain.id))))
                    .append($('<div class="panel-body"></div>')
                            .append(_dl));
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

            var panelBody = $('<div class="row"></div>')
                    .append($('<div class="col-xs-12"></div>')
                            .append($('<form class="form-horizontal"></form>')
                                    .append(createFormGroup('Domain name', 'name', domain.name))));

            var panel = $('<div class="panel panel-primary"></div>')
                    .append($('<div class="panel-heading"></div>')
                            .append($('<h3 class="panel-title">Edit Domain</h3>')))
                    .append($('<div class="panel-body"></div>')
                            .append(panelBody));

            _body.append($('<div class="col-xs-12"></div>')
                    .append(panel));
        }

        function createFormGroup(label, name, value) {
            return $('<div class="form-group"></div>')
                    .append($('<label></label>')
                            .attr({
                                for: name,
                                class: 'col-sm-2 control-label'
                            })
                            .html(label))
                    .append($('<div class="col-sm-10"></div>')
                            .append($('<input />')
                                    .attr({
                                        type: 'text',
                                        class: 'form-control',
                                        id: name,
                                        name: name,
                                        value: value
                                    })));
        }

        function deleteDomain(id) {
            if (confirm("Are you sure you want to delete domain (ID=" + id + ")?")) {
                console.log('todo: deletedomain');
                getAllDomains();
            }
        }

        function convertDomain(id) {
            if (confirm("Are you sure you want to convert domain (ID=" + id + ")?")) {
                console.log('todo: convertdomain');
                getAllDomains();
            }
        }
    </script>
@endsection