(function($) {
  $.fn.scrollabletable2 = function(options){
	  var defaults = {
      'padding_right' : 15,
      'max_height_scrollable' : 500,
      'max_width' : 1380,
      'scroll_horizontal' : 1,
		  };
      settings = $.extend({},defaults,options);    
      var add_pixel = 0,minus_width = 30;
     /*
      if($.browser.chrome){
          add_pixel = 1;
          minus_width = 18;
       }
       else {
          add_pixel = 0;
          minus_width = 30;
       }
      */  
     var _cur_table = this;
     var _class_table = _cur_table.attr('class'); 
     var virtualTable = $('<table></table>');
     var virtualThead = $('<thead></thead>');
     var _th , _text , _tr , _div , _td; 

     var _cur_thead = _cur_table.find('thead');
     var tag_pattern = new RegExp(/<([a-z]+).*>/);
     /* buat header virtual untuk scroll vertical */
     _cur_table.wrap('<div class="table_wrapper_kj"></div>');
     $(_cur_thead).find('tr').each(function(){
        _tr = $('<tr></tr>');
          $(this).find('th').each(function(){
            _th = $(this).clone();
            if(tag_pattern.test(_th.html())){
            	_text = _th.html();
            }
            else{
            	_text = _th.text();
            }	
            _div = $('<div></div>')
                   .html(_text)
                   .css({
                    'width' : $(this).width() + 2, // tambahkan 2px supaya sama di chroome dan opera
                   //       'height': $(this).height()
                   });
            _th.html(_div);
            _th.appendTo(_tr);
            // isi th yang asli diganti dengan _div, supaya benar2 sama
            $(this).html(_div.clone());
          });
         _tr.appendTo(virtualThead);   
        });    

      virtualThead.appendTo(virtualTable);
      virtualTable.insertAfter(_cur_table);
      virtualTable.wrap('<div class="div_header_virtual_kj"></div>');
      virtualTable.addClass(_class_table);
      virtualTable.parent('.div_header_virtual_kj').css({
        'position' : 'absolute',
        'top' : _cur_table.position().top,
        'left' : _cur_table.position().left,
        'max-width': settings.max_width - settings.padding_right,
        'background-color': '#FFFFFF',
        'overflow-x' : 'hidden',
      });
      virtualTable.css({
        'margin-bottom': '1px',
        'width' : _cur_table.width() - settings.padding_right,
      });
      
      delete virtualThead;
      _tr = null;
      _th = null;
      _div = null;
  	/* buat scroll horizontal */
      var scrollHorizontal = true;
      scrollHorizontal = (_cur_table.width() - $(window).width()) < minus_width ? false : true;
      
      if(scrollHorizontal && settings.scroll_horizontal){
    	  var _cur_tbody = _cur_table.find('tbody');
    	    /* buat virtual header untuk kiri atas */
    	    var virtualTopLeftTable = $('<table></table>');
    	    var virtualTopLeftThead = $('<thead></thead>');
    	    virtualTopLeftTable.addClass(_class_table);
    	    virtualTable.find('tr').each(function(){
    	      _tr = $('<tr></tr>');
    	      $(this).find('th.ftl').each(function(){
    	        _th = $(this).clone();
    	        _th.find('div').css({'height':$(this).height() + add_pixel,'display':'table-cell','vertical-align':'middle'});
    	        _th.appendTo(_tr);
    	      });
    	      _tr.appendTo(virtualTopLeftThead);
    	    });
    	    virtualTopLeftThead.appendTo(virtualTopLeftTable);
    	 /* tambahkan pada urutan yang paling akhir, agar posisinya bisa diatas */
    	    virtualTopLeftTable.css({
    	 //     'margin-bottom': '1px',
    	 //     'height': virtualTable.height() 
    	    }).insertAfter(_cur_table);

    	    virtualTopLeftTable.wrap('<div class="top_left_header_virtual"></div>');
    	    virtualTopLeftTable.parent('.top_left_header_virtual').css({
    	      'position' : 'absolute',
    	      'top' : _cur_table.position().top,
    	      'left' : _cur_table.position().left,
    	      'background-color': '#FFFFFF',
    	      'padding' : '0px',
    	      'z-index' : 99
    	    });
    	    
    	    /* buat left virtual side */
    	    var virtualLeftTable = $('<table></table>');
    	    var virtualLeftTbody = $('<tbody></tbody>'); 
    	    var _virtualLeftThead = virtualTopLeftThead.clone();
    	    var parent_this_tr;
    	    virtualLeftTable.addClass(_cur_table.attr('class'));
    	    _virtualLeftThead.appendTo(virtualLeftTable);
    	    _cur_tbody.find('tr').each(function(){
    	      parent_this_tr = $(this);
    	      if($(this).hasClass('rekap')){
    	        _tr = $('<tr></tr>').addClass('rekap');  
    	      }
    	      else {
    	        _tr = $('<tr></tr>');
    	      }
    	      
    	      $(this).find('td.ftl').each(function(){
    	        _td = $(this).clone();
    	        _text = _td.html();
    	     
    	        _div = $('<div></div>')
    	                  .html(_text)
    	                  .css({
    	                    'display':'table-cell',
    	                    'vertical-align':'middle',
    	                    'height':$(this).height() + add_pixel,
    	                    'width' : $(this).width()              
    	                  });
    	        _td.html(_div);          
    	        _td.appendTo(_tr);
    	  
    	      });
    	      /* ubah isi dari td terakhir dengan div supaya benar2 sama */
    	 /*
    	      parent_this_tr.children('td.ftl').each(function(index){
    	    	  $(this).html(_tr.children('td.ftl').eq(index).html());
    	      })
    	 */     
    	      _tr.appendTo(virtualLeftTbody);
    	    });
    	    virtualLeftTbody.appendTo(virtualLeftTable);
    	    virtualLeftTable.css({
    	      'margin-bottom': '0px',
    	     /* 'height': $('#laporan-production').height() */   
    	    }).insertAfter(_cur_table);

    	    virtualLeftTable.wrap('<div class="left_section_virtual"></div>');
    	    virtualLeftTable.parent('.left_section_virtual').css({
    	      'position' : 'absolute',
    	      'top' : _cur_table.position().top,
    	      'left' : _cur_table.position().left,
    	      'background-color': '#FFFFFF',
    	      'padding' : '0px',
    	      'max-height' : settings.max_height_scrollable - settings.padding_right + 'px',
    	      'overflow-y' : 'hidden',
    	    });
    	}
  	  _cur_table.parent('.table_wrapper_kj').css({
      'padding' : '0px',
      'padding-bottom' : settings.padding_right,
      'max-height' : settings.max_height_scrollable,
      'max-width': settings.max_width,
      'overflow' : 'auto',
      'background-color': '#FFFFFF',
    });
    
    delete virtualTopLeftTable;
    delete virtualTable;
    delete virtualLeftTbody;
    delete virtualLeftTable;
    delete _tr;
    delete _td;
    delete _div;
 /*   
    _cur_table.parent('.table_wrapper_kj').scroll(function(){
      var maxScrollTop = document.getElementById('left_section_virtual') != undefined ? document.getElementById('left_section_virtual').scrollHeight - $('#left_section_virtual').height() : 10;
      var maxScrollLeft = document.getElementById('div_header_virtual_kj') != undefined ? document.getElementById('div_header_virtual_kj').scrollWidth - $('#div_header_virtual_kj').width() : 10;
      if(scrollHorizontal){
    	  if($(this).scrollTop() > maxScrollTop){
    	        $(this).scrollTop(maxScrollTop);
    	      }
    	  $('#left_section_virtual').scrollTop($(this).scrollTop());
      }
      
      if($(this).scrollLeft() > maxScrollLeft){
        $(this).scrollLeft(maxScrollLeft);
      }
      $('#div_header_virtual_kj').scrollLeft($(this).scrollLeft());
      
    });
 */   
	  return this;
	  };

}( jQuery ));
