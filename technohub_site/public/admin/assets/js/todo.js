/*
Author       : Dreamstechnologies
Template Name: Smarthr - Bootstrap Admin Template
*/

(function () {
    "use strict";
	
	// Todo Strike Content
	$('.todo-item input').on('click', function(){
		$(this).parent().parent().toggleClass('todo-strike');
	});

	$('.todo-inbox-check input').on('click', function(){
		$(this).parent().parent().toggleClass('todo-strike-content');
	});

	$('.todo-list input').on('click', function(){
		$(this).parent().parent().toggleClass('todo-strike-content');
	});

})();