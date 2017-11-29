jQuery(function ($) {

    'use strict';
		
		// -------------------------------------------------------------
		//  Background Slider Image Source
		// -------------------------------------------------------------

		(function () {

		    if (sliderImages == undefined) {
		        sliderImages = [ "/templates/diagram/images/bg/1.jpg","/templates/diagram/images/bg/2.jpg","/templates/diagram/images/bg/3.jpg"];
            }
            if (editMode == undefined) {
                editMode = false;
            }

	       //backstretch slide for main body
	        $(".home-section").backstretch(sliderImages, {fade: 750,duration: 3000});

            if (editMode) {
                $(".home-section").backstretch("pause");
            }

	    }());
   

 
	
    // -------------------------------------------------------------
    //  Owl Carousel
    // -------------------------------------------------------------

    (function() {

        $("#recent-projects").owlCarousel({
            items:4,
			responsive:{
				0:{
					items:1
				},
				600:{
					items:2
				},
				1000:{
					items:4
				}
			},
            nav:true,
            autoplay: !editMode,
            dots:false,
            navText: [
              "<i class='fa fa-angle-left'></i>",
              "<i class='fa fa-angle-right'></i>"
            ],            
       
        });

    }()); 

    (function() {

        $(".testimonial-slider, #product-carousel").owlCarousel({
            items:1,
            nav:false,
            autoplay: !editMode,
			dots:true,
        });

    }());
	
	(function() {

        $("#brand-carousel").owlCarousel({
            items:6,
			responsive:{
				0:{
					items:3,
				},
				600:{
					items:4,
				},
				1000:{
					items:6,
				}
			},
            nav:false,
            autoplay:true,
        });

    }());

    // -------------------------------------------------------------
    //  Cubeportfolio
    // -------------------------------------------------------------

    
    (function () {
        
        $('#projects').cubeportfolio({
            filters: '#project-menu',
            animationType: 'sequentially',
            gapVertical: 30,
            gapHorizontal: 30, 

	        });

    }()); 

		
	// -------------------------------------------------------------
    // Progress Bar
    // -------------------------------------------------------------
 
    $('.skill').bind('inview', function(event, visible, visiblePartX, visiblePartY) {
        if (visible) {
            $.each($('div.progress-bar'),function(){
                $(this).css('width', $(this).attr('aria-valuenow')+'%');
            });
            $(this).unbind('inview');
        }
    });  

    // -------------------------------------------------------------
    //  Magnific Popup
    // -------------------------------------------------------------

    (function() {

        $('.image-link').magnificPopup({
          type: 'image',
          gallery:{
            enabled:true,
          }
        });

    }());   

	
	
	
	
	
// script end
});

	
	
	
	

