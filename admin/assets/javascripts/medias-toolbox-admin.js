(function($)
{
	'use strict';
    $(function()
    {
        if($('.input-status').length > 0)
        {
            $('.input-status').on('click',function()
            {
                if($(this).is(':checked'))
    		    {
    		       	$("#handler-renamer").show(100);
    		    }
    		    else
    		    {
    		    	$("#handler-renamer").hide(500);
    		    }
    		});

    		if($("#handler-renamer.show").length)
    		{
    		    $("#handler-renamer").show();
    		}
        }
    });
})(jQuery);