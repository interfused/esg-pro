jQuery(function($){
	// simple multiple select
	$( "select:visible" ).select2({ width: "element" }); // Only fire on visible inputs to begin with.
	$( document.body ).on( "focus", ".ptitle,select",
		function ( ev ) {
			if ( ev.target.nodeName === "SELECT" ) {
				// Fire for this element only
				$( this ).select2({ width: "element" });
			} else {
				// Fire again, but only for selects that haven't yet been select2'd
				$( "select:visible" ).not( ".select2-offscreen" ).select2({
					width: "element"
				});
			}
		}
	);
});