'use strict';
(function() {
	document.addEventListener('DOMContentLoaded', function() {
		let map = null;
		let marker = null;
		let geocodeTimeout = null;
		
		// Функція для отримання адреси за координатами
		function getAddressFromCoordinates(lat, lng) {
			// Оновлюємо статус
			const statusElement = document.getElementById('addressStatus');
			if (statusElement) {
				statusElement.textContent = "Отримуємо адресу...";
				statusElement.className = "address-status loading";
			}
			
			// Використовуємо CORS проксі для уникнення проблем з CORS
			const proxyUrl = 'https://corsproxy.io/?';
			// Додаємо більш детальні параметри для отримання повнішої адреси
			const nominatimUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&namedetails=1&accept-language=uk`;
			
			fetch(proxyUrl + encodeURIComponent(nominatimUrl))
				.then(response => {
					if (!response.ok) {
						throw new Error('Мережева помилка');
					}
					return response.json();
				})
				.then(data => {
					if (data && data.address) {
						// Форматуємо адресу: Країна, Область, Місто, Район міста, Вулиця, Будинок
						const addr = data.address;
						let formattedAddress = '';
						
						// Країна
						if (addr.country) formattedAddress += addr.country;
						
						// Область
						if (addr.state) formattedAddress += (formattedAddress ? ', ' : '') + addr.state;
						
						// Місто/село
						let cityAdded = false;
						if (addr.city) {
							formattedAddress += (formattedAddress ? ', ' : '') + addr.city;
							cityAdded = true;
						} else if (addr.town) {
							formattedAddress += (formattedAddress ? ', ' : '') + addr.town;
							cityAdded = true;
						} else if (addr.village) {
							formattedAddress += (formattedAddress ? ', ' : '') + addr.village;
							cityAdded = true;
						}
						
						// Район міста (пріоритет для міських районів)
						// Спершу перевіряємо міські райони, потім загальні
						if (addr.residential && cityAdded) {
							// suburb зазвичай є районом міста
							formattedAddress += (formattedAddress ? ', ' : '') + addr.residential;
						} else if (addr.borough && cityAdded) {
							// city_district - це також міський район
							formattedAddress += (formattedAddress ? ', ' : '') + addr.borough;
						} else if (addr.district && !cityAdded) {
							// district використовуємо тільки якщо немає міста (для сіл/селищ)
							formattedAddress += (formattedAddress ? ', ' : '') + addr.district;
						}
						
						// Вулиця
						if (addr.road) {
							formattedAddress += (formattedAddress ? ', ' : '') + addr.road;
						}
						
						// Номер будинку
						if (addr.house_number) {
							formattedAddress += (formattedAddress ? ', ' : '') + addr.house_number;
						}
						
						// Якщо адреса все ще порожня, використовуємо display_name
						if (!formattedAddress && data.display_name) {
							formattedAddress = data.display_name;
						}
						
						const addressField = document.getElementById('address');
						if (addressField) addressField.value = formattedAddress;
						
						if (statusElement) {
							statusElement.textContent = "Адресу отримано успішно";
							statusElement.className = "address-status success";
						}
						
					} else {
						const addressField = document.getElementById('address');
						if (addressField) addressField.value = "Адресу не знайдено";
						
						if (statusElement) {
							statusElement.textContent = "Адресу не знайдено для цих координат";
							statusElement.className = "address-status error";
						}
					}
				})
				.catch(error => {
					console.error('Помилка отримання адреси:', error);
					const addressField = document.getElementById('address');
					if (addressField) addressField.value = "Не вдалося отримати адресу";
					
					if (statusElement) {
						statusElement.textContent = "Помилка отримання адреси. Спробуйте пізніше.";
						statusElement.className = "address-status error";
					}
				});
		}
		
		// Функція для оновлення позиції
		function updatePosition(latLng) {
			// Оновлюємо координати в полях вводу
			const latField = document.getElementById('latitude');
			const lngField = document.getElementById('longitude');
			
			if (latField) latField.value = latLng.lat.toFixed(6);
			if (lngField) lngField.value = latLng.lng.toFixed(6);
			
			// Додаємо затримку для дебаунсу запитів
			if (geocodeTimeout) {
				clearTimeout(geocodeTimeout);
			}
			
			geocodeTimeout = setTimeout(() => {
				getAddressFromCoordinates(latLng.lat, latLng.lng);
			}, 800);
		}
		
		// Обробник події відкриття модального вікна
		const geoModal = document.getElementById('geoModal');
		if (geoModal) {
			geoModal.addEventListener('shown.bs.modal', function() {
				// Ініціалізація карти тільки після відкриття модального вікна
				if (!map) {
					// Центруємо на Дніпрі (для тестування)
					map = L.map('map-container').setView([48.465, 35.04], 13);
					
					// Додаємо tiles (шари) карти
					L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
					}).addTo(map);
					
					// Додаємо маркер по центру карти
					marker = L.marker(map.getCenter(), {
						draggable: true,
						title: "Перетягніть мене"
					}).addTo(map);
					
					// Оновлюємо координати при переміщенні маркера
					marker.on('dragend', function() {
						updatePosition(marker.getLatLng());
					});
					
					// Оновлюємо координати при переміщенні карти
					map.on('move', function() {
						if (marker) {
							marker.setLatLng(map.getCenter());
							updatePosition(map.getCenter());
						}
					});
					
					// Оновлюємо координати при зміні масштабу
					map.on('zoomend', function() {
						if (marker) {
							updatePosition(map.getCenter());
						}
					});
					
					// Ініціалізуємо значення координат
					updatePosition(map.getCenter());
				} else {
					// Якщо карта вже існує, оновлюємо її розмір
					setTimeout(function() {
						map.invalidateSize();
						updatePosition(map.getCenter());
					}, 100);
				}
			});
			
			// Обробник події закриття модального вікна
			geoModal.addEventListener('hidden.bs.modal', function() {
				// Видаляємо карту при закритті модального вікна
				if (map) {
					map.remove();
					map = null;
					marker = null;
				}
			});
		}
		
		// Обробник кнопки збереження
		const saveButton = document.getElementById('saveLocation');
		if (saveButton) {
			saveButton.addEventListener('click', function() {
				const addressField = document.getElementById('address');
				const latField = document.getElementById('latitude');
				const lngField = document.getElementById('longitude');
				
				const address = addressField ? addressField.value : '';
				const lat = latField ? latField.value : '';
				const lng = lngField ? lngField.value : '';
				
				alert(`Локацію збережено!\nАдреса: ${address}\nКоординати: ${lat}, ${lng}`);
				
				// Закриваємо модальне вікно після збереження
				const modal = bootstrap.Modal.getInstance(geoModal);
				if (modal) {
					modal.hide();
				}
			});
		}
	});
})();