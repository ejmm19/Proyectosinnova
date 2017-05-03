<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
/* Add your JavaScript code here.
                     
If you are using the jQuery library, then don't forget to wrap your code inside jQuery.ready() as follows:

jQuery(document).ready(function( $ ){
    // Your code in here 
});

End of comment */ 

jQuery(document).ready( function() {
     jQuery(window).on('scroll', function(){
     if (jQuery(window).scrollTop()>220) {
       jQuery(".pt-navbar").css('height', '100px');

     }else{
      jQuery(".pt-navbar").css('height', '125px');

     }
    });


   jQuery(".hvr-underline-from-center").text('Leer Más');

  
jQuery(function(){

     jQuery('a[href*=#]').click(function() {

     if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
         && location.hostname == this.hostname) {

             var $target = jQuery(this.hash);

             $target = $target.length && $target || jQuery('[name=' + this.hash.slice(1) +']');

             if ($target.length) {

                 var targetOffset = $target.offset().top;

                 jQuery('html,body').animate({scrollTop: targetOffset}, 1000);

                 return false;

            }

       }

   });

});
  
  
});
</script>
<!-- end Simple Custom CSS and JS -->
