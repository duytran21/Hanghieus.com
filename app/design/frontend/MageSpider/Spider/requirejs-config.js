var config = {
	deps: [
		"js/main"
	],
    paths: {
        'slick': 'js/vendor/jquery/slick.min'
    },
    shim: {
        'slick': {
            deps: ['jquery'],
            exports: 'jQuery.fn.slick',
        }
    }
};