/*************************************************************************
    @title      : Combobox Multi Columns Plugin
    @requirement: jquery-1.7.2.js or above 
    @author     : mursitoitudwi
    @website    : http://greenhole.web.id
    @date       : Thursday, 09/10/2014
    @param      : 1) spacesToAdd = (Int) jumlah spasi.
                  2) biggestLength = (Int) jumlah string terbesar.
                  3) columnSeparator = (String) jenis pembatas.
                  4) fontWeight = (String) ukuran huruf option.

    -- Usage --

    1) Tentukan selector-nya.
    2) Isikan teks/string di option dengan menggunakan separator kolom 
       dan tambahkan class `header` jika ingin menambah kolom header.
       [note: jangan kosongkan value dari option yg menjadi pilihannya]. 
    3) Sertakan bersama selector-nya.
    4) Congrate... [ Still Learning ]

    -- Example --

    on javascript : 

        $(function(){
            $('select.contohmulticolumn').combomulticolumn();
        });

    on html :
    
        <select class="contohmulticolumn">
                <option value="">Pilihan : </option>
                <option class="header">Kavling +Umur(Hari) +Tonase</option>
                <option value="1">AB 12-1 +12 +90000</option>
                <option value="2">D 2-7 +10 +80000</option>
                <option value="3">F 2-1 +2 +70000</option>
        </select>    

    -- End --

 *************************************************************************/

(function($){
    $.fn.combomulticolumn = function (options) {
        var defaults = {
                spacesToAdd: -10,
                biggestLength: 0,
                columnSeparator: '+',
                fontWeight: '600'
        };
        settings = $.extend({}, defaults, options);

        var elm = this;

        elm.css({'font-family':'"Courier New", Courier, monospace','font-weight':settings.fontWeight});

        elm.find("option[value!='']").each(function () {
            var len = $(this).text().length;
            if (len > settings.biggestLength) {
                settings.biggestLength = len;
            }
        });

        var padLength = settings.biggestLength + settings.spacesToAdd;

        elm.find("option[value!='']").each(function () {
            var parts = $(this).text().split(settings.columnSeparator);
            var union = [];
            var column= [];
            
            for(var ii=0; ii<parts.length-1; ii++){
                var strLength = parts[ii].length;
            
                for(var x=0; x<(padLength-strLength); x++){
                    parts[ii] = parts[ii]+' '; 
                }
                union.push(parts[ii].replace(/ /g,'\u00a0'));
            }
            
            for(var jj=0; jj<parts.length; jj++){
                column.push($.trim(parts[jj]));
            }
            var n = parseInt(parts.length)-1;
            $.merge(union,parts[n]);
            var r = union.join('');
            
            $(this).text(r);
            $(this).closest('option[class!="header"]')
            .attr('columndata',column.join(';'));

            $(this).closest('option.header')
            .attr('disabled',true)
            .css({'font-weight':'bold','border-top':'1px solid','border-bottom':'1px solid','padding':'1px','margin':'1px'});
        });
    return this;
    };
})(jQuery);