"use strict";
// product-magnifier var
	var devita_magnifier_vars;
	var yith_magnifier_options = {
		
		sliderOptions: {
			responsive: devita_magnifier_vars.responsive,
			circular: devita_magnifier_vars.circular,
			infinite: devita_magnifier_vars.infinite,
			direction: 'left',
            debug: false,
            auto: false,
            align: 'left',
            height: 'auto',
            //height: "100%", //turn vertical
            //width: 100,  
			prev    : {
				button  : "#slider-prev",
				key     : "left"
			},
			next    : {
				button  : "#slider-next",
				key     : "right"
			},
			scroll : {
				items     : 1,
				pauseOnHover: true
			},
			items   : {
				visible: Number(devita_magnifier_vars.visible),
			},
			swipe : {
				onTouch:    true,
				onMouse:    true
			},
			mousewheel : {
				items: 1
			}
		},
		
		showTitle: false,
		zoomWidth: devita_magnifier_vars.zoomWidth,
		zoomHeight: devita_magnifier_vars.zoomHeight,
		position: devita_magnifier_vars.position,
		lensOpacity: devita_magnifier_vars.lensOpacity,
		softFocus: devita_magnifier_vars.softFocus,
		adjustY: 0,
		disableRightClick: false,
		phoneBehavior: devita_magnifier_vars.phoneBehavior,
		loadingLabel: devita_magnifier_vars.loadingLabel,
	};