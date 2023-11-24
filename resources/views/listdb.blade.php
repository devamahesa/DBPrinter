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

</script>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/dblogin') }}"><b>DB</b>Printer</a>
        </div>

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Pilih Database</h3>
                <div class="card-tools">
                    <!-- Buttons, labels, and many other things can be placed here! -->
                    <!-- Here is a label for example -->
                    <button type="button" class="btn btn-tool" data-widget="remove"><i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>

            <!-- /.card-header -->
            <div class="card-body">
                <form action="tablelist" method="post" id="form">
                    {{csrf_field()}}
                    @if($errors->any())
                    <div class="invalid" id="invalid">
                        <b>{{$errors->first()}}</b><br>
                    </div>
                    @endif
                    <!-- Database Driver -->
                    <p>Database Driver</p>
                    <div class="input-group mb-3">
                        <input class="form-control" type="text" name="driver" value="{{$database_driver==="sqlsrv"? "SQL SERVER" : "ORACLE"}}" required readonly><br>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-database"></span></div>
                        </div>
                    </div>

                    <!-- Database/schema List -->
                    <p>Pilih database yang akan di-<i>print</i></p>
                    <div class="input-group mb-3">
                        <select class="form-control" name="db_printed" required>
                            <option value="" hidden selected disabled>-- Pilih Database --</option>
                            @if($database_driver==='sqlsrv')
                            @for($i = 0; $i< count($querydb); $i++) <option value={{$querydb[$i]->name}}>{{$querydb[$i]->name}}</option>
                                @endfor
                                @else
                                @for($i = 0; $i< count($querydb); $i++) <option value={{$querydb[$i]->schema}}>{{$querydb[$i]->schema}}</option>
                                    @endfor
                                    @endif
                        </select>
                    </div>

                    <!-- Button -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-info btn-block">
                            Pilih
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->

    </div>
</body>

</html>
