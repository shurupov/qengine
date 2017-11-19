(function($){

    "use strict";

    $("#rev_slider_3_1").show().revolution({
        sliderType:"standard",
        sliderLayout:"fullwidth",
        dottedOverlay:"none",
        delay:9000,
        navigation: {
            keyboardNavigation:"off",
            keyboard_direction: "horizontal",
            mouseScrollNavigation:"off",
            mouseScrollReverse:"default",
            onHoverStop:"off",
            touch:{
                touchenabled:"on",
                swipe_threshold: 75,
                swipe_min_touches: 1,
                swipe_direction: "horizontal",
                drag_block_vertical: false
            }
            ,
            bullets: {
                enable:true,
                hide_onmobile:false,
                style:"ares",
                hide_onleave:false,
                direction:"horizontal",
                h_align:"right",
                v_align:"bottom",
                h_offset:40,
                v_offset:40,
                space:15,

            }
        },
        visibilityLevels:[1240,1024,778,480],
        gridwidth:1170,
        gridheight:785,
        lazyType:"none",
        shadow:0,
        spinner:"spinner2",
        stopLoop: (editMode ? 'on' : 'off'),
        stopAfterLoops: (editMode ? 0 : -1),
        stopAtSlide: (editMode ? 1 : -1),
        shuffle:"off",
        autoHeight:"off",
        disableProgressBar:"on",
        hideThumbsOnMobile:"off",
        hideSliderAtLimit:0,
        hideCaptionAtLimit:0,
        hideAllCaptionAtLilmit:0,
        debugMode:false,
        fallbacks: {
            simplifyAll:"off",
            nextSlideOnWindowFocus:"off",
            disableFocusListener:false,
        }
    });

})(jQuery);