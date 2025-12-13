"use strict";
(function () {
	$(document).ready(function() {
		// Додаємо обробники подій для модального вікна
		$('#geoModal').on('show.bs.modal', function () {
			$('.my-dropdown-geo-btn').addClass('active');
		});
		
		$('#geoModal').on('hide.bs.modal', function () {
			$('.my-dropdown-geo-btn').removeClass('active');
		});

// Додатково: обробник для події hidden (коли модалка повністю прихована)
		$('#geoModal').on('hidden.bs.modal', function () {
			$('.my-dropdown-geo-btn').removeClass('active');
		});
		// Відкриття/закриття головного меню по кнопці
		$('#btn-open-menu').on('click', function() {
			$('.my-dropdown-list-wrapper').toggle();
			$(this).toggleClass('active');
			$('.my-dropdown-search-wrapper').hide();
		});
		
		// Обробник для введення тексту в поле вводу
		$('.my-dropdown-input').on('input', function() {
			const searchText = $(this).val().trim(); // Отримуємо текст з поля вводу
			
			if (searchText.length > 0) {
				// Якщо текст введено, відображаємо блок пошуку
				$('.my-dropdown-search-wrapper').show();
				$('.my-dropdown-list-wrapper').hide(); // Приховуємо основний блок
			} else {
				// Якщо поле вводу порожнє, приховуємо блок пошуку
				$('.my-dropdown-search-wrapper').hide();
				$('.my-dropdown-list-wrapper').show(); // Показуємо основний блок
			}
		});
		
		// Обробник для вибору країни
		$('.my-dropdown-item-radio[name="country"]').on('click', function() {
			// Закриваємо всі внутрішні блоки
			$('.my-dropdown-next-list').hide();
			
			// Відкриваємо внутрішній блок для обраної країни
			$(this).closest('.my-dropdown-item').find('.my-dropdown-next-list').show();
			
			// Закриваємо правий блок, якщо він був відкритий
			$('.my-dropdown-list.second').hide();
			clearSecondBlockCheckboxes(); // Очищаємо чекбокси при закритті блоку
			
			// Очищаємо вибір областей у попередній країні
			clearDistrictRadios();
		});
		
		// Обробник для вибору області
		$('.my-dropdown-item-radio[name="district"]').on('click', function() {
			// Перевіряємо, чи обрана радіокнопка
			if ($(this).is(':checked')) {
				// Відкриваємо правий блок
				$('.my-dropdown-list.second').show();
			}
		});
		
		// Обробник для вибору чекбокса міста
		$('.my-dropdown-item-checkbox').on('change', function() {
			// Отримуємо внутрішній блок, який потрібно відобразити/приховати
			const nextList = $(this).closest('.my-dropdown-item').find('.my-dropdown-next-list');
			
			// Якщо чекбокс обраний, відображаємо внутрішній блок
			if ($(this).is(':checked')) {
				nextList.show();
			} else {
				// Якщо чекбокс не обраний, приховуємо внутрішній блок
				nextList.hide();
				clearInnerCheckboxes(nextList); // Очищаємо внутрішні чекбокси
			}
		});
		
		// Обробник для вибору чекбокса району (другий рівень)
		$('.my-dropdown-next-list .my-dropdown-item-checkbox').on('change', function() {
			// Отримуємо внутрішній блок третього рівня
			const nextNextList = $(this).closest('.my-dropdown-item').find('.my-dropdown-next-next-list');
			
			// Якщо чекбокс обраний, відображаємо блок третього рівня
			if ($(this).is(':checked')) {
				nextNextList.show();
			} else {
				// Якщо чекбокс не обраний, приховуємо блок третього рівня
				nextNextList.hide();
				clearInnerCheckboxes(nextNextList); // Очищаємо чекбокси третього рівня
			}
		});
		
		// Закриття меню при кліку поза ним
		$(document).on('click', function(event) {
			if (!$(event.target).closest('.my-dropdown').length) {
				$('.my-dropdown-list-wrapper').hide();
				$('.my-dropdown-search-wrapper').hide(); // Приховуємо блок пошуку
				$('#btn-open-menu').removeClass('active');
				clearSecondBlockCheckboxes(); // Очищаємо чекбокси при закритті меню
			}
		});
		
		// Додаткова логіка для відображення вкладених елементів при виборі радіокнопки
		$('.my-dropdown-item-radio').on('change', function() {
			// Якщо обрана радіокнопка країни
			if ($(this).attr('name') === 'country') {
				// Закриваємо всі внутрішні блоки
				$('.my-dropdown-next-list').hide();
				
				// Відкриваємо внутрішній блок для обраної країни
				$(this).closest('.my-dropdown-item').find('.my-dropdown-next-list').show();
				
				// Закриваємо правий блок
				$('.my-dropdown-list.second').hide();
				clearSecondBlockCheckboxes(); // Очищаємо чекбокси при закритті блоку
				
				// Очищаємо вибір областей у попередній країні
				clearDistrictRadios();
			}
			
			// Якщо обрана радіокнопка області
			if ($(this).attr('name') === 'district') {
				// Перевіряємо, чи обрана радіокнопка
				if ($(this).is(':checked')) {
					// Відкриваємо правий блок
					$('.my-dropdown-list.second').show();
				}
			}
		});
		
		// Функція для очищення чекбоксів у блоці .my-dropdown-list.second
		function clearSecondBlockCheckboxes() {
			$('.my-dropdown-list.second .my-dropdown-item-checkbox').each(function() {
				$(this).prop('checked', false); // Скидаємо стан чекбокса
				$(this).closest('.my-dropdown-item').find('.my-dropdown-next-list').hide(); // Приховуємо внутрішній блок
			});
		}
		
		// Функція для очищення внутрішніх чекбоксів
		function clearInnerCheckboxes(parentElement) {
			parentElement.find('.my-dropdown-item-checkbox').each(function() {
				$(this).prop('checked', false); // Скидаємо стан чекбокса
			});
		}
		
		// Функція для очищення радіокнопок областей
		function clearDistrictRadios() {
			$('.my-dropdown-next-list .my-dropdown-item-radio[name="district"]').each(function() {
				$(this).prop('checked', false); // Скидаємо стан радіокнопки
			});
		}
	});
})();