<!-- <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css"> -->
    <!-- Javascripts -->
<!-- <script src="assets/libs/jquery/jquery-2.0.0.min.js"></script> -->
<script src="assets/libs/popper/popper.min.js"></script>
<!-- <script src="assets/libs/bootstrap/js/bootstrap.min.js"></script> -->
<!-- Project Files -->
<!-- <link rel="stylesheet" href="assets/libs/bootstrap/css/jquery.bootstrap.year.calendar.css">
<script src="assets/libs/bootstrap/js/jquery.bootstrap.year.calendar.js"></script> -->


<div class="container">
    <div id="calendar" class="calendar"></div>
</div>

<div class="modal fade" id="modal_kalenderlibur" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Master - Kalender libur</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" value="" name="hstatus" />

                    <div class="row">
                        <div class="form-group col-md-1">
                            <label class="control-label">dari</label>
                        </div>
                        <div class="form-group col-md-4">                            
                            <div class="form-control input-group">
                                <input type="text" readonly="" value="" class="form-control" name="dari">
                                <input type="hidden" name="ddari" class="date-picker" />
                            </div>
                        </div>

                        <div class="form-group col-md-2">&nbsp;</div>

                        <div class="form-group col-md-1">
                            <label class="control-label">sampai</label>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-control input-group">
                                <input type="text" value="" class="date-picker form-control" name="sampai">
                                <input type="hidden" name="dsampai" class="date-picker" />
                            </div>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label">Keterangan</label>
                        <div class="form-control input-group">
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer" style="margin: 0px; padding: 3px;">
                <div class="pull-right">
                    <button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary">Simpan</button>
                    <button type="button" name="tombolBatal" id="btnBatal" class="btn btn-primary">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_listlibur" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Daftar hari libur</h4>
            </div>

            <div class="modal-body">

            </div>
        </div>
    </div>
</div>


<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
<style>
div#modal_kalenderlibur > .modal-dialog {
    width: 70% !important;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}
.border-top {
    border-top: 1px solid #dee2e6 !important;
}
</style>


<link rel="stylesheet" href="assets/libs/bootstrap/css/jquery.bootstrap.year.calendar.css" />
<script type="text/javascript" src="assets/libs/bootstrap/js/jquery.bootstrap.year.calendar.js"></script>
<link rel="stylesheet" href="assets/libs/jquery-ui/css/jquery-ui.min.css" />
<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery.ui.datepicker-id.js"></script>
<!-- <script type="text/javascript" src="assets/libs/bootbox/js/bootbox.js"></script> -->
<script>
var vari = {
    ds : <?php echo $kalenderlibur; ?>
}, baseUrl = "<?php echo base_url(); ?>";

</script>

<script type="text/javascript" src="assets/js/ycb4.js"></script>