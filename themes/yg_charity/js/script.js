(function ($, Drupal) {
'use strict';
$(document).ready(function() {
  $('.owl-carousel').owlCarousel({
    loop: true,
    margin: 10,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false
      },
      600: {
        items: 3,
        nav: false
      },
      1000: {
        items: 3,
        nav: false,
        loop: false,
        margin: 20
      }
    }
  })
})
$(".nav li.dropdown").hover(
    function(){
      $(this).addClass("open");
    },function(){
      $(this).removeClass("open");
    }
  );
$('li.dropdown a.dropdown-toggle').once('dropdown').click(function(e) {
      window.location = $(this).attr('href');
    });
})(jQuery, Drupal);