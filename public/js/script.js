$(function(){
	"use strict";
	/*=========================================================================
		Initializing stellar.js Plugin
	=========================================================================*/
	$('.section').stellar({
		horizontalScrolling: false
	});

	$(window).on('load', function(){
		$('body').addClass('loaded');
	});
});