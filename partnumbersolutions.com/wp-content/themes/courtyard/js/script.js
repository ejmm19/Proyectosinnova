jQuery(document).ready( function() {
     jQuery(window).on('scroll', function(){
     if (jQuery(window).scrollTop()>220) {
       jQuery(".pt-navbar").css('height', '100px');

     }else{
      jQuery(".pt-navbar").css('height', '125px');

     }
    });


   jQuery(".hvr-underline-from-center").text('Leer Más');

});
