<!DOCTYPE html>
@extends('adminlte::master')
<html lang="en">

<head>
    <title>Database Printer</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    //function for checkbox select all
    $(document).ready(function() {
        $('#select-all').click(function(event) {
            if (this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        })

        function table_selected(frm) {
            var selchbox = []; // array that will store the value of selected checkboxes
            // gets all the input tags in frm, and their number
            var inpfields = frm.getElementsByTagName('input');
            var nr_inpfields = inpfields.length;

            for (var i = 0; i < nr_inpfields; i++) {
                if (inpfields[i].type == 'checkbox' && inpfields[i].checked == true)
                    selchbox.push(inpfields[i].value);
            }
            return selchbox;
        }
        document.getElementById('btntest').onclick = function() {
            var selchb = table_selected(this.form);
        }

        $('#uncheckall').click(function(event) {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        })
    });

</script>

<body class="hold-transition login-page">
    <script src="{{ asset('js/app.js') }}"></script>
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/dblogin') }}"><b>DB</b>Printer</a>
        </div>
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Print Dokumen</h3>
                <div class="card-tools">
                    <!-- Buttons, labels, and many other things can be placed here! -->
                    <!-- Here is a label for example -->

                    <span class="badge badge-light badge-pill">{{count($table_name)}} Tabel</span>
                    <button type="button" class="btn btn-tool" data-widget="remove">
                        <i class="fas fa-back"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>

            <!-- /.card-header -->
            <div class="card-body">

                @if(count($table_name)==0)
                <p>Tidak Ada Data</p>
                @else
                <form action="printdb" method="post">
                    {{csrf_field()}}
                    @if($errors->any())
                    <div class="invalid" id="invalid">
                        <b>{{$errors->first()}}</b><br>
                    </div>
                    @endif

                    <!--Show all tables-->
                    <div>
                        <p>Pilih Tabel</p>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-outline-info btn-block" data-toggle="modal" data-target="#staticBackdrop">
                            List Tabel
                        </button><br>
                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div>
                                            <div>
                                                <h4 class="modal-title fs-5" id="staticBackdropLabel">List Tabel</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <table class="table table-hover" id=example-search-input>
                                                <tbody>
                                                    <?php $k=0 ?>
                                                    @for($i=0; $i < (ceil(count($table_name)/4)); $i++) <tr>
                                                        @for($j = 0; $j < 4; $j++) @if($k<count($table_name)) <td>
                                                            <input class="form-check-input" type="checkbox" value="{{$table_name[$k]}}" name="list[]">{{$table_name[$k]}}
                                                            </td>
                                                            @endif
                                                            <?php $k=$k+1?>
                                                            @endfor
                                                            </tr>
                                                            @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <div>
                                            <a><input id="select-all" class="form-check-input" type="checkbox">Pilih Semua</a>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Pilih</button>
                                            <button type="button" id="uncheckall" class="btn btn-danger" data-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--get extension printed-->
                    <div>
                        <p>Pilih Ekstensi</p>
                        <div class="input-group mb-3">
                            <select class="form-control" name="export_ext" id="export_ext" required>
                                <option value="" hidden selected disabled>-- Pilih Tipe File --</option>
                                <option value="docx" required>.docx</option>
                                <option value="pdf" required>.pdf</option>
                            </select>
                        </div>
                        <button id="btntest" type="submit" class="btn btn-info">Print</button>
                    </div>
                </form>
                @endif
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
</body>
</html>
