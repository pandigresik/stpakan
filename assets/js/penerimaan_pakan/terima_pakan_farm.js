TerimaPakan = {
    terima : function(elm){
        bootbox.confirm("Apakah anda yakin akan menerima pakan",function(r){
            if(r){
                /** simpan ke database */
                var _form = $(elm).closest('form');
                var _no_do = _form.find('input[name=no_do]').val();
                var _no_reg = _form.find('select[name=no_reg]').val();
                var _flok_bdy = _form.find('select[name=no_reg] option:selected').data('flok');
                var _kode_barang = _form.find('select[name=kode_barang]').val();
                var _kuantitas = _form.find('input[name=kuantitas]').val();
                
                $.ajax({
                    url : 'penerimaan_pakan/terima_pakan/simpan',
                    data : { no_do : _no_do, no_reg : _no_reg, flok : _flok_bdy, kode_barang : _kode_barang, kuantitas : _kuantitas},                    
                    type : 'post',
                    beforeSend : function(){
                        $(elm).hide();
                    },
                    success: function(data){
                        if(data.status){
                            _form.find('input').val('');
                            $(elm).show();
                        }
                        bootbox.alert(data.message);    
                    },
                    datatype : 'json'
                })
            }
        })
    }
}