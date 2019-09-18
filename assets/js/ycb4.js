$(function(){

    $('.date-picker').datepicker({ 
        dateFormat: 'dd-mm-yy',
        beforeShow: function() {
            setTimeout(function(){
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        }
    });
    
    var ds = vari.ds;

    $('#calendar').calendar({datasource: ds}, ds);

    var Kalenderlibur = {
        reloadpage : function(){
            $.ajax({
                type : 'GET',
                dataType : 'html',
                url : baseUrl + 'master/Kalenderlibur'
            }).done(function(data) {
                $('#main_content').html(data);
            });

            $('.modal-backdrop').remove();
            return;
        },
        resetinput : function(){
            $('textarea[name=keterangan]').val('');
            $('.date-picker').datepicker('setDate', 'today');
        },
        daterange : function(dari, sampai){
            var dari = dari.datepicker("getDate"),
                sampai = sampai.datepicker("getDate"),
                currentDate = new Date(dari.getTime()),
                between = [];

            while (currentDate <= sampai) {
                var perdate = new Date(currentDate);

                var d = perdate.getDate();
                var m = (perdate.getMonth() + 1);
                var y = perdate.getFullYear();

                var formatpush = [y, m, d].join('-');

                between.push(formatpush);

                currentDate.setDate(currentDate.getDate() + 1);
            }

            return between;
        }       
    };


    function notificationBox(message){
        bootbox.dialog({
            message : message,
            buttons : {
                success : {
                    label : "OK",
                    className : "btn-primary",
                    callback : function() {
                        return true;
                    }
                }
            }
        });
    }


    $('#btnBatal').click(function(){
        $('#modal_kalenderlibur').modal("hide");
    });


    $('#btnSimpan').click(function(){
        var dari = $("input[name=ddari]"),
            sampai = $("input[name=sampai]"),
            daterange = Kalenderlibur.daterange(dari, sampai),
            keterangan = $('textarea[name=keterangan]').val();

        var url_ubah = ($.trim(keterangan).length < 1) ? "master/kalenderlibur/delete" : "master/kalenderlibur/edit"

        $.ajax({
            type : 'POST',
            dataType : 'json',
            data : {
                keterangan : keterangan,
                daterange : daterange
            },
            url : ($('input[name=hstatus]').val() == 'baru') ? "master/kalenderlibur/add" : url_ubah
        }).done(function(data) {
            var strnotif = '';
            $.each(data, function(i,v){
                strnotif += v.tgl + " : " + v.message + "\n";
            });

            notificationBox(strnotif);
            $('#modal_kalenderlibur').modal("hide");      
            Kalenderlibur.resetinput();

            Kalenderlibur.reloadpage();
        });
    });
});    