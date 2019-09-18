<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Command 
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <textarea class="form-control" name="query" id="" cols="30" rows="10">Letakkan query anda disini</textarea>    
                    </div>
                    <div class="col-md-12 new-line">
                        <form class="form form-inline" action="post" onsubmit="return false">                            
                            <label for="">Limit</label>
                            <input class="form-control" size="2" type="text" value="50" name="limit" id="">
                            <div class="btn btn-default" onclick="Adminer.execute(this)">Jalankan</div>
                            <div class="pull-right">
                                <div class="btn btn-default" onclick="Adminer.select(this)">Select</div>
                                <div class="btn btn-default" onclick="Adminer.insert(this)">Insert</div>
                                <div class="btn btn-default" onclick="Adminer.update(this)">Update</div>
                                <div class="btn btn-default" onclick="Adminer.delete(this)">Delete</div>                                                                                     
                            </div>

                        </form>
                    </div>
                </div>
            </div>     
            <div class="panel panel-default">
                <div class="panel-heading">
                    Hasil Query
                </div>
                <div class="panel-body" id="queryResult">
                    
                </div>
                <div class="panel-footer">
                    <div class="btn btn-default" onclick="Adminer.sinkron(this)">Create Sinkronisasi</div>           
                </div>
            </div>                                 
        </div>
    </div>
</div>