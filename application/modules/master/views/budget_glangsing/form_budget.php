<div class="panel-body detail-do">
        <div class="new-line">
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-md-5 control-label text-right">Kode Budget</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control" id="inp_id_budget" name="inp_id_budget" placeholder="Kode Budget">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-5 control-label text-right">Kategori Budget</label>

                        <div class="col-md-5">
                            <select onchange="goSearch();" id="inp_kategori" name="inp_kategori" class="form-control">
                                <option value="I">Internal</option>
                                <option value="E">Eksternal</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-5 control-label text-right">Nama Budget</label>

                        <div class="col-md-7">
                            <input type="text" class="form-control" id="inp_nama_budget" name="inp_nama_budget" placeholder="Nama Budget">
                        </div>
                    </div>
                    <div class="form-group" id="grp-status">
                        <label for="" class="col-md-5 control-label text-right">Status</label>

                        <div class="col-md-5">
                            <label><input type="checkbox" name="inp_status" id="inp_status" checked="checked"> Aktif</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-5 control-label text-right"></label>

                        <div class="col-md-6">
							<input type="hidden" id="prop">
                            <a id="btnSimpan" class="btn btn-primary">Confirm</a>
                            <a href="#" class="btn" data-dismiss="modal">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript" src="assets/js/master/budget_glangsing.js"></script>