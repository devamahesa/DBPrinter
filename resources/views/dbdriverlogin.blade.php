<!DOCTYPE html>
@extends('adminlte::master')
<html lang="en">
<head>
    <title>Database Printer</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#service_name").hide();
        $('#driver').on('change', function togglefields() {
            if (this.value == 'oracle') {
                $("#service_name").show();
            } else {
                $("#service_name").hide();
            }
        });
    })

</script>

<body class="hold-transition login-page">
    <script src="{{ asset('js/app.js') }}"></script>
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/dblogin') }}"><b>DB</b>Printer</a>
        </div>
        <!-- /.login-logo -->

        <!-- /.login-box-body -->
        <div class="card">
            <div class="card-body login-card-body">
                <form action="authdb" method="post">
                    {{csrf_field()}}
                    @if(session()->has('error'))
                    <div class="invalid"><b>
                            {{session()->get('error')}}
                        </b></div>
                    @endif
                    <p>Login ke Database</p>

                    <div class="input-group mb-3">
                        <select id="driver" name="driver" class="form-control" required>
                            <option value="x" hidden selected>-- Pilih Database Driver --</option>
                            <option value="sqlsrv" required>MS SQL Server</option>
                            <option value="oracle" required>Oracle SQL Plus</option>
                        </select><br>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-database"></span></div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input class="form-control" type="text" name="host" value="{{old('host')}}" placeholder="Host" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-globe"></span></div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input class="form-control" type="text" name="port" value="{{old('port')}}" placeholder="Port" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-plug"></span></div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input class="form-control" type="text" name="username" value="{{old('username')}}" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                    </div>


                    <div class="input-group mb-3">
                        <input class="form-control" type="password" name="password" value="" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>


                    <div class="input-group mb-3" id="service_name">
                        <input class="form-control" type="text" name="service_name" value="{{old('service_name')}}" onchange="togglefields()" placeholder="Service Name">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class=""></span></div>
                        </div>
                    </div>

                    <div class="col-4">
                        <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#myModal">
                            Submit
                        </button>
                    </div>

                    <!-- SHOW MODAL -->
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-body">
                                    <h4 class="modal-title">Apakah anda yakin ?</h4>
                                    <!--
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button> -->
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-info">Ya</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tidak</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
