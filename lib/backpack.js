history.navigationMode = 'compatible';
$(window).load(function(){
	//put array into javascript variable
	
	$(".tooltip").hide();
	
	$(".item").mouseout(function() { 
		$(this).find(".tooltip").hide();
	}).mouseover(function() {
		$(this).find(".tooltip").show();
		$(this).find(".tooltext").show();
	});

	function removeHRS()
	{
		$('.floatclear').remove();
		$('.item').unwrap();
	}
	
	function addHRS()
	{
		var divs = $('.backpack > .item');
		for(var i = 0; i < divs.length; i+=50) {
		  divs.slice(i, i+50).wrapAll("<div class='backpack_partition'></div>");
		}
		$('.backpack_partition').after('<HR class="floatclear" \>');
	}
	
	function addBlanks()
	{
		var items = $('.backpack > .item');

		for (var i=1;i<items.length;i++)
		{
			var exists = $('.item[inventory_position=' + i + ']').length;
			if (exists == 0 && i-1 > 0 && i-1 < items.length)
			{
				var insert = ('<div class="item" inventory_position="'+ i + '" item="" id="none"></div>');
				var pos = i-1;
				$('.item[inventory_position=' + pos + ']').after(insert);
			}
		}
	}

	$('.filter').click(function() {
		$('.item').show();
	   
		$('.filter').each(function() { //for each checkbox
			var item_type = $(this).attr('name');
			var is_checked = $(this).is(':checked');
			if (!is_checked) $('.backpack').find('#' + item_type).hide();
		});
		removeHRS();
		addHRS();
	});
	
	$('.filter').each(function(){ //outputs number of each item
		var type = $(this).attr('name');
		var len = $('.item').siblings("#"+type).size();
		$(this).next().append('('+len+')');
	});
		 
	if ($(".dropform option:selected").val()!= "") $(".dropform").change();              
	$(".dropform").change(function(){
		var sortby = $(".dropform option:selected").val();
		if (sortby == "sort_by_name")
		{
			removeHRS();
			$('.item').tsort('',{attr:'item'});
			$('.backpack').find('#none').remove();
		}
		else if (sortby == "sort_by_quality")
		{
			removeHRS();
			$('.item').tsort('',{attr:'id'});
			$('.backpack').find('#none').remove();
		}
		else if (sortby == "sort_by_backpack") 
		{
			removeHRS();
			$('.item').tsort('',{attr:'inventory_position'});
			addBlanks();
		}
		addHRS();
		$('backpack_partition').each(function(){
			if ($(this).children().length==0) 
			{
				$(this).next().remove();
				$(this).remove();
				
			}
		});
	});
});   
