"use strict";

import {
	PhotoLoaderMini,
} from "./function_on_pages-create.js";

$("#agency-branch-metro").select2({
	width: 'resolve',
	placeholder: '-',
	minimumResultsForSearch: -1,
});

$(".js-example-responsive2").select2({
	width: 'resolve',
	minimumResultsForSearch: -1,
});
new PhotoLoaderMini({
	inputIdSelector: '#loading-photo',
	wrapperClassSelector: '.photo-info-list',
});