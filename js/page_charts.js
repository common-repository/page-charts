jQuery(document).ready( function( $ ){
	$('.js_file').keypress(function(e){
		saved = $(this).parent().parent().children().eq(1).children().first().attr('saved');
		if(saved ==''|| saved ==null){
			alert('Save php file first');
			$(this).val('');
			e.preventDefault();
			return;
		}
	})

	var data1,js_name;
	$('.show_chart').click(function(){
		libraryName = $( "#submit_library option:selected" ).val();
		if(libraryName!=='highcharts'&&libraryName!=='chart'){
			alert('Choose a chart library!');
			return;
		}
		chart_id = $(this).parent().parent().attr('id');
		var data = {
			action: 'save_chart',
		};
     	data['file_name'] = 'show_chart';
		data['chart_id'] = chart_id;
		root = $('#chart_table').attr('root');
		php_name = data['php_name'] =  $(this).parent().parent().children().eq(1).children().first().val();
		js_name = data['js_name'] =  $(this).parent().parent().children().eq(2).children().first().val();
		
		if(js_name == null||js_name =='') return;
		jQuery.post( ajaxurl, data, function( data ){
			data1 = data;
			console.log(root)
			if(typeof data['plagin_error'] === 'undefined'){
				$.get(root+"/js/"+js_name+".js", function(data){
				}, 'text').done(function(data2) {
					data2 = data2.toLowerCase (data2);
					data2 = data2.replace(/\s+/g, '');
				
					// Highcharts library
					
					if(libraryName == 'highcharts'){
						begin = data2.indexOf('highcharts.chart(')
						if(begin >-1){
							sim = data2.substring(begin+17,begin+18);
							if(sim =="'"||sim =='"'){
								data2 = data2.substring(begin+18)
								end = data2.indexOf(sim);
								container_name = data2.substring(0,end);
							}
						}
						if(typeof container_name === 'undefined'){
							begin = data2.indexOf('renderto:');
								if(begin >-1){
									sim = data2.substring(begin+9,begin+10);
									data2 = data2.substring(begin+10);
									end = data2.indexOf(sim);
									container_name = data2.substring(0,end);
								}
						}if(typeof container_name === 'undefined'){
							var end = 0;
							do {
								begin = data2.indexOf('#',end);
								if(begin == -1){
									break;
								}
								end = begin+1;
								var sim='';
								while( sim != '"' && sim != "'"){
									sim = data2.substring(end,end+1); 
									++end;
								}
								if(data2.substring(end,end+12) == ").highcharts"){
									container_name = data2.substring(begin+1,end-1);
									break;
								}
							
							}while(begin >-1)
				
						}
					}else if(libraryName == 'chart'){
				
					// Chart library
					
						begin = data2.indexOf('newchart(');
						if(begin >-1){
							keyWord = data2.substring(begin+9,begin+33);
							if(keyWord == 'document.getelementbyid('){
								sim = data2.substring(begin+33,begin+34);
								end = data2.indexOf(sim,begin+35);
								container_name = data2.substring(begin+34,end);						
							}
						    
						}if(typeof container_name === 'undefined'){
						
							sim = data2.substring(begin+9,begin+10);
							if(sim != '"' && sim != "'"){
								end = data2.indexOf(",",begin+10);	
								ctx = data2.substring(begin+9,end);
								begin = data2.indexOf(ctx+'=');
								openBracketInd = data2.indexOf('(',begin+1);
								closeBracketInd = data2.indexOf(')',begin+1);
								name = data2.substring(openBracketInd+2,closeBracketInd-1);
								name = name.replace('#','');
								if(name.length>1)container_name = name;;
							}
						}
					}
					if(typeof container_name === 'undefined'){
						$('#chart_append').css('display','block');
						return;
					}
				    $('#cont_name'+chart_id).val(container_name);
					$('.container').attr('id',(container_name));
					if(typeof container_name === 'undefined')return;				
					window.data = data1
					$.getScript(root+"/js/"+js_name+".js", function(){
                    });
		        }).fail(function() {
						alert( "error of getting js file "+js_name+".js" );
					});
 			}
			else {
				alert(data['plagin_error']);
				return;
			};
		});
	})

    $('.save_post').click(function(){
	    if($(this).attr('class') == 'save_post delete'){
		    var conf = confirm("Delete the chart?");
			if(conf == true){}
			else {
				 return
			};
	    }
		var data = {
			action: 'save_chart',
			page_name : $(this).parent().parent().children().first().children().first().attr('name'),
			post_type : $(this).parent().parent().children().first().children().first().attr('post_type'),
			file_name : $(this).parent().children().first().val(),
			chart_nm : $(this).parent().children().first().attr('chart_nm'),
			chart_id : $(this).parent().parent().attr('id'),
			file_type : $(this).attr('id'),
			php_name : $(this).parent().parent().children().eq(1).children().first().val(),
			js_name : $(this).parent().parent().children().eq(2).children().first().val()
		};
		
		jQuery.post( ajaxurl, data, function( response ){
			 window.location=document.location.href;
		});
	   
    })
	var curVal = $('#submit_library').val();
	if (curVal == 'chart' ||curVal == 'highcharts') {
        $('#submit_library').css('color','black');
    } else {
        $('#submit_library').css('color','red');
    }
    $('#submit_library').change(function() {
		
		var curVal = $('#submit_library').val();
		if (curVal == 'chart' ||curVal == 'highcharts') {
           $('#submit_library').css('color','black');
		} else {
           $('#submit_library').css('color','red');
		}
		
		var data = {
			action: 'save_library',
			library_name : $(this).val()
		};
		jQuery.post( ajaxurl, data, function( response ){
			 window.location=document.location.href;
		});
	})

	$('#subnum').click(function(){ 
		container_name = $('#cont_name').val();
	    $('.container').attr('id',(container_name));
		window.data = data1
		$.getScript(root+"/js/"+js_name+".js", function(){});
	   })
})
