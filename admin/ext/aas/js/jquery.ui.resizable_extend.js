/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: extend resizable so not have a bug with elem jump to top or left position
  
*/
(function($){
	var _init = $.ui.dialog.prototype._init;
 
	$.ui.resizable.prototype._init = function() {
		var self = this;
    _init.apply(this, arguments);
 
	};
	$.extend($.ui.resizable.prototype, {
		
	 	_mouseDrag: function(event) {

		var data,
			el = this.helper, props = {},
			smp = this.originalMousePosition,
			a = this.axis,
			prevTop = this.position.top,
			prevLeft = this.position.left,
			prevWidth = this.size.width,
			prevHeight = this.size.height,
			dx = (event.pageX-smp.left)||0,
			dy = (event.pageY-smp.top)||0,
			trigger = this._change[a];

		if (!trigger) return false;

		data = trigger.apply(this, [event, dx, dy]);
		this._updateVirtualBoundaries(event.shiftKey);
		if (this._aspectRatio || event.shiftKey) data = this._updateRatio(data, event);
		
		data = this._respectSize(data, event);
		this._updateCache(data);
		this._propagate("resize", event);
		//if (this.position.top !== prevTop) {props.top = this.position.top + "px";}
		//if (this.position.left !== prevLeft) props.left = this.position.left + "px";
		if (this.size.width !== prevWidth) props.width = this.size.width + "px";
		if (this.size.height !== prevHeight) props.height = this.size.height + "px";
		el.css(props);
		if (!this._helper && this._proportionallyResizeElements.length) this._proportionallyResize();

		if ( ! $.isEmptyObject(props) ) this._trigger("resize", event, this.ui());
		return false;
	}
 	
		
		
	});
})(jQuery);
