// Contact Us Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const phoneInput = document.getElementById('phone');
    const startLiveChatBtn = document.getElementById('startLiveChat');

    // Sanitize and format phone number
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d+]/g, ''); // Keep only digits and +
        if (value.startsWith('+61')) {
            value = value.slice(0, 12); // Limit to +61 and 10 digits
        } else if (value.startsWith('0')) {
            value = '+61' + value.slice(1, 11); // Convert 0X to +61 X
        } else if (value.startsWith('4')) {
            value = '+61' + value.slice(0, 10); // Convert 04X to +61 4X
        } else {
            value = '+61' + value.slice(0, 10); // Default to +61 and 10 digits
        }

        // Format as +61 4XX XXX XXX or +61 X XXXX XXXX
        if (value.length >= 12) {
            const digits = value.replace('+61', '');
            if (digits.startsWith('4')) {
                e.target.value = `+61 ${digits.slice(0, 3)} ${digits.slice(3, 6)} ${digits.slice(6, 10)}`;
            } else if (digits.startsWith('0')) {
                e.target.value = `+61 ${digits.slice(0, 1)} ${digits.slice(1, 5)} ${digits.slice(5, 9)}`;
            }
        } else {
            e.target.value = value;
        }
    });

    // Submit form validation
    form.addEventListener('submit', function(e) {
        const phone = phoneInput.value;
        const phoneRegex = /^\+?61\s?(4\d{2}|0[2-8])\s?\d{3}\s?\d{3}$/;
        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Please enter a valid Australian phone number (e.g., +61 4XX XXX XXX or +61 2 XXXX XXXX).');
            return;
        }
    });

    // Trigger Tawk.to chat on button click
    startLiveChatBtn.addEventListener('click', function() {
        if (typeof Tawk_API !== 'undefined' && Tawk_API.maximize) {
            Tawk_API.maximize(); // Open the chat widget
        } else {
            alert('Live chat is not available. Please try again later or contact us at info@janiking.com.');
        }
    });

    // Popup functionality
    const learnMoreButtons = document.querySelectorAll('.learn-more');
    const modal = document.createElement('div');
    modal.className = 'modal';
    document.body.appendChild(modal);

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modal.appendChild(modalContent);

    const closeBtn = document.createElement('span');
    closeBtn.className = 'close';
    closeBtn.innerHTML = '&times;';
    modalContent.appendChild(closeBtn);

    const content = document.createElement('p');
    modalContent.appendChild(content);

    learnMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            let text = '';
            switch (target) {
                case 'franchise-support':
                    text = 'JaniKing provides ongoing franchise support, including marketing assistance, operational guidance, and access to national accounts to ensure your success.';
                    break;
                case 'training-programs':
                    text = 'Our comprehensive training programs cover cleaning techniques, business management, and safety protocols, preparing you for a thriving franchise.';
                    break;
                case 'franchise-faq':
                    text = 'Frequently Asked Questions include investment details, training duration, and support options. Visit our FAQ page for more info.';
                    break;
            }
            content.textContent = text;
            modal.style.display = 'block';
        });
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Initialize Leaflet map with JaniKing locations
    const map = L.map('map').setView([20.0, 0.0], 2); // Center on world, zoom level 2
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Sample JaniKing locations (replace with real data)
    const locations = [
        { lat: 32.7767, lng: -96.7970, title: "Dallas, USA" },
        { lat: 43.6510, lng: -79.3470, title: "Toronto, Canada" },
        { lat: 51.5074, lng: -0.1278, title: "London, UK" },
        { lat: -33.8688, lng: 151.2093, title: "Sydney, Australia" },
        { lat: 35.6762, lng: 139.6503, title: "Tokyo, Japan" },
    ];

    // Add markers for each location
    locations.forEach((location) => {
        L.marker([location.lat, location.lng]).addTo(map)
            .bindPopup(location.title)
            .openPopup();
    });

    // Fit map to include all markers
    const bounds = L.latLngBounds(locations.map(loc => [loc.lat, loc.lng]));
    map.fitBounds(bounds);
});