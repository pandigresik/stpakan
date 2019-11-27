<div class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Verifikasi DO</h3>
                            </div>
                            <div class="panel-body">
                                <form class="form-horizontal" role="form" onsubmit="return Verifikasi.verifikasi_do(this)">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label for="nomerdo" class="control-label">Pencarian No. DO</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="nomerdo" onchange="Verifikasi.cek_do(this)" maxlength='8'>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-default">Konfirmasi</button>
                                        </div>
                                    </div>
                                </form>
                                <div id="detail_do">
                                
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
      <script type="text/javascript" src="assets/js/verifikasi_do/verifikasi.js"></script>  