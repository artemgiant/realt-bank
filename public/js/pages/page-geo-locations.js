import {HoverOnInformationContact} from "./info-agent-or-contact-modal.js";

new HoverOnInformationContact({
	containerSelector:'.geo-locations-wrapper',
	hoverAttribute:'data-hover-contact',
	modalClass:'info-contact-modal',
}); // Для контактів

(function () {
	$('.geo-locations-header .my-custom-input input, .geo-list .my-custom-input input').on('change', function() {
		const $this = $(this);
		
		if ($this.parents('.geo-locations-header').length) {
			$('.geo-list .my-custom-input input').prop('checked', $this.prop('checked'));
		} else {
			const total = $('.geo-list .my-custom-input input').length;
			$('.geo-locations-header .my-custom-input input')
				.prop('checked', $('.geo-list .my-custom-input input:checked').length === total);
		}
	});
	
	const initTooltips = function () {
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
	};
	
	// Викликаємо ініціалізацію Tooltip після створення таблиці
	initTooltips();
})();