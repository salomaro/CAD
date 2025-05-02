const map = L.map('map').setView([19.4326, -99.1332], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = null;

        map.on('click', function(e) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('coordenadas').value = e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
        });

        document.getElementById('search-button').addEventListener('click', function() {
            const address = document.getElementById('address-input').value;
            if (address.trim() === '') return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);

                        if (marker) {
                            map.removeLayer(marker);
                        }

                        map.setView([lat, lon], 15);
                        marker = L.marker([lat, lon]).addTo(map);
                        document.getElementById('coordenadas').value = lat.toFixed(6) + ', ' + lon.toFixed(6);
                    } else {
                        alert('Ubicación no encontrada');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al buscar la ubicación');
                });
        });

        const chatInput = document.querySelector('.chat-input input');
        const chatButton = document.querySelector('.chat-input button');
        const chatMessages = document.querySelector('.chat-messages');

        function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                const msgElement = document.createElement('div');
                msgElement.classList.add('message', 'sent');
                msgElement.textContent = message;
                chatMessages.appendChild(msgElement);
                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                setTimeout(() => {
                    const responses = [
                        "Mensaje recibido",
                        "Procediendo según protocolo",
                        "Unidad en camino",
                        "Necesitamos más información"
                    ];
                    const response = responses[Math.floor(Math.random() * responses.length)];

                    const replyElement = document.createElement('div');
                    replyElement.classList.add('message', 'received');
                    replyElement.textContent = response;
                    chatMessages.appendChild(replyElement);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1000);
            }
        }

        chatButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        document.querySelectorAll('.resource-option').forEach(option => {
            option.addEventListener('click', function() {
                const resourceType = this.querySelector('span').textContent;
                alert(`Recurso asignado: ${resourceType}`);
            });
        });