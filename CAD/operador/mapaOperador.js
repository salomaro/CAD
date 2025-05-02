  // Inicializar mapa
  const map = L.map('map').setView([19.4326, -99.1332], 12);
        
  // Añadir capa de mapa base
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);
  
  // Marcador para la ubicación
  let marker = null;
  
  // Evento para colocar marcador al hacer clic en el mapa
  map.on('click', function(e) {
      if (marker) {
          map.removeLayer(marker);
      }
      marker = L.marker(e.latlng).addTo(map);
      document.getElementById('coordenadas').value = e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
  });
  
  // Buscar ubicación
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

  function validarFormulario() {
      // Validar que se haya seleccionado una ubicación
      const coordenadas = document.getElementById('coordenadas').value;
      if (!coordenadas) {
          alert('Por favor seleccione una ubicación en el mapa');
          return false;
      }
      
      // Validar otros campos si es necesario
      if (!document.getElementById('paso').value) {
          alert('Por favor describa qué pasó');
          return false;
      }
      
      return true;
  }