

document.addEventListener('DOMContentLoaded', () => {
    const hotelsContainer = document.getElementById('hotels-container');
    const hotelDetailsContent = document.getElementById('hotel-details-content');
    const searchInput = document.getElementById('hotel-search');
    const filterButtons = document.querySelectorAll('.filter-chip');
  
    const safeIsSubfolder = (typeof isSubfolder !== 'undefined') ? isSubfolder : window.location.pathname.includes('/php/') || window.location.pathname.includes('/destinations/');
    
    const basePath = window.location.pathname.includes('/php/') ? '../' : '';
    const hotelPagePath = safeIsSubfolder ? 'hotel.php' : 'php/hotel.php';
    const diagnosticPath = safeIsSubfolder ? 'test_db.php' : 'php/test_db.php';

    let allHotels = [];

  

    async function fetchHotels(category = 'all') {
        try {
            const response = await fetch(`${basePath}api/get_hotels.php?category=${category}`);
            let data = await response.json();
            if (data.error) throw new Error(data.error);
          
            
            return data;
        } catch (error) {
            console.error('Hotel fetch failed:', error);
            return { error: true, message: error.message };
        }
    }

    async function fetchHotelBySlug(slug) {
        try {
            const response = await fetch(`${basePath}api/get_hotels.php?slug=${slug}`);
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            return data;
        } catch (error) {
            console.error('Hotel details fetch failed:', error);
            return null;
        }
    }

    function getGalleryImages(hotel) {
        const urls = (hotel.gallery || '')
            .split(',')
            .map(url => url.trim())
            .filter(Boolean);

        if (hotel.image_url && !urls.includes(hotel.image_url) && !urls.includes(basePath + hotel.image_url)) {
            urls.unshift(hotel.image_url);
        }

        if (urls.length === 0) {
            urls.push(basePath + 'images/default-hotel.jpg'); 
        }

        return [...new Set(urls.map(url => {
            if (!url.startsWith('http') && !url.startsWith('../') && safeIsSubfolder) {
                return '../' + url;
            }
            return url;
        }))];
    }

    

    function renderHotels(hotels) {
        if (!hotelsContainer) return;

        
        if (hotels.error) {
            hotelsContainer.innerHTML = `
                <div class="empty-state error-state">
                    <i class="fas fa-database"></i>
                    <h3>Database Connection Issue</h3>
                    <p>We couldn't connect to the hotel database. Please ensure:</p>
                    <ul class="hotels-empty-error-list">
                        <li>1. MySQL is started in XAMPP Control Panel.</li>
                        <li>2. Database 'globetrek_db' exists in phpMyAdmin.</li>
                        <li>3. 'database.sql' has been successfully imported.</li>
                    </ul>
                    <a href="${diagnosticPath}" target="_blank" class="btn-book hotels-btn-diagnostic">Run Diagnostic Test</a>
                </div>
            `;
            return;
        }

        if (hotels.length === 0) {
            hotelsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>No stays found matching your criteria.</p>
                </div>
            `;
            return;
        }

        hotelsContainer.innerHTML = hotels.map(hotel => `
            <div class="hotel-card reveal visible" data-category="${hotel.category}">
                <div class="hotel-img-wrap">
                    <a href="${basePath}php/hotel.php?slug=${hotel.slug}" aria-label="Open ${hotel.name} hotel details">
                        <img src="${safeIsSubfolder && !hotel.image_url.startsWith('../') ? '../' + hotel.image_url : hotel.image_url}" alt="${hotel.name}" onerror="this.src='${safeIsSubfolder ? '../' : ''}images/ellaview.jpg'">
                    </a>
                    <span class="hotel-badge">${(hotel.category || 'hotel').charAt(0).toUpperCase() + (hotel.category || 'hotel').slice(1)}</span>
                    <span class="hotel-photo-count"><i class="fas fa-camera"></i> ${getGalleryImages(hotel).length} photos</span>
                    <button class="hotel-wishlist" aria-label="Add to wishlist"><i class="far fa-heart"></i></button>
                </div>
                <div class="hotel-content">
                    <div class="hotel-meta">
                        <div class="hotel-star">
                            ${Array(parseInt(hotel.rating) || 5).fill('<i class="fas fa-star"></i>').join('')}
                        </div>
                        <span class="hotel-category">${(hotel.category || 'hotel').toUpperCase()} RETREAT</span>
                    </div>
                    <h3 class="hotel-title">${hotel.name || 'Unknown Hotel'}</h3>
                    <div class="hotel-location"><i class="fas fa-map-marker-alt"></i> ${hotel.location || 'Unknown Location'}</div>

                    <div class="hotel-amenities">
                        ${(hotel.amenities || '').split(',').filter(Boolean).slice(0, 3).map(a => `<span class="amenity" title="${a.trim()}"><i class="fas fa-check-circle"></i></span>`).join('')}
                    </div>

                    <div class="hotel-booking">
                        <span class="hotel-price">$${hotel.price_per_night} <small>/night</small></span>
                        <div class="hotel-actions"> 
                            <a href="${basePath}php/hotel.php?slug=${hotel.slug}" class="btn-book">View <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

   

    function renderHotelDetails(hotel) {
        if (!hotelDetailsContent) return;

        if (!hotel) {
            hotelDetailsContent.innerHTML = '<div class="container"><p>Hotel not found.</p></div>';
            return;
        }

        const galleryImages = getGalleryImages(hotel);
        const coverImage = galleryImages[0];
        const amenities = (hotel.amenities || '').split(',').map(item => item.trim()).filter(Boolean);

        document.title = `${hotel.name} | GlobeTrek Adventures`;

        hotelDetailsContent.innerHTML = `
            <!-- Details Hero -->
            <section class="hotel-details-hero" style="background-image: url('${coverImage}')">
                <div class="hero-overlay"></div>
                <div class="container reveal visible">
                    <div class="hero-path">
                        <a href="index.php">Home</a> / <a href="accommodation.html">Accommodation</a> / ${hotel.name}
                    </div>
                    <h1>${hotel.name}</h1>
                    <div class="hero-location"><i class="fas fa-map-marker-alt"></i> ${hotel.location}</div>
                </div>
            </section>

            <!-- Content Grid -->
            <section class="hotel-details-body section">
                <div class="container">
                    <div class="details-grid">
                        <div class="details-main reveal visible">
                            <div class="content-block">
                                <h2>About this experience</h2>
                                <p>${hotel.description}</p>
                            </div>

                            <div class="content-block">
                                <h2>Room & Hotel Photos</h2>
                                <div class="hotel-gallery-container">
                                    <button class="gallery-nav-btn prev-btn" id="gallery-prev" aria-label="Previous image">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <div class="hotel-feature-slider" id="hotel-gallery-slider">
                                        ${galleryImages.map(url => `
                                            <div class="gallery-item-wrap" onclick="openLightbox('${url.trim()}')">
                                                <img src="${url.trim()}" alt="Gallery image" onerror="this.src='images/ellaview.jpg'">
                                                <div class="gallery-hover"><i class="fas fa-expand-alt"></i></div>
                                            </div>
                                        `).join('')}
                                    </div>
                                    <button class="gallery-nav-btn next-btn" id="gallery-next" aria-label="Next image">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="content-block">
                                <h2>Premium Amenities</h2>
                                <div class="amenities-grid">
                                    ${amenities.map(a => `
                                        <div class="amenity-item">
                                            <i class="fas fa-check"></i>
                                            <span>${a.trim()}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="content-block">
                                <h2>Location</h2>
                                <div class="mini-map-placeholder">
                                    <img src="https://picsum.photos/seed/map/800/400" alt="Map">
                                    <div class="map-overlay">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <span>View on Interactive Map</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End details-main -->

                        <aside class="details-sidebar reveal visible">
                            <div class="booking-sticky-card">
                                <div class="price-header">
                                    <span class="price-val">$${hotel.price_per_night}</span>
                                    <span class="price-unit">/ night</span>
                                </div>
                                <div class="rating-summary">
                                    ${Array(parseInt(hotel.rating)).fill('<i class="fas fa-star"></i>').join('')}
                                    <span>(120 reviews)</span>
                                </div>

                                <div class="booking-mini-form">
                                    <div class="field-group">
                                        <label>Check-in / Check-out</label>
                                        <div class="date-ranges">
                                            <input type="date" id="check-in">
                                            <input type="date" id="check-out">
                                        </div>
                                    </div>
                                    <div class="field-group">
                                        <label>Guests</label>
                                        <select id="guest-count">
                                            <option>1 Guest</option>
                                            <option selected>2 Guests</option>
                                            <option>3 Guests</option>
                                            <option>4 Guests</option>
                                        </select>
                                    </div>
                                    <div class="price-calc" id="price-calc">
                                        <div class="calc-row">
                                            <span>$${hotel.price_per_night} x 3 nights</span>
                                            <span>$${hotel.price_per_night * 3}</span>
                                        </div>
                                        <div class="calc-row">
                                            <span>Service fee</span>
                                            <span>$20</span>
                                        </div>
                                        <div class="calc-total">
                                            <span>Total</span>
                                            <span>$${(hotel.price_per_night * 3) + 20}</span>
                                        </div>
                                    </div>
                                    <a href="booking.php?type=hotel&hotel_id=${hotel.slug}&category=${hotel.category}" class="btn-book-premium">
                                        Book Now <i class="fas fa-bolt"></i>
                                    </a>
                                </div>
                                <p class="card-note"><i class="fas fa-info-circle"></i> Best price guaranteed on GlobeTrek</p>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        `;

        
        setupPriceCalculation(hotel.price_per_night);
        initHotelGallerySlider();
    }

    function initHotelGallerySlider() {
        const slider = document.getElementById('hotel-gallery-slider');
        const prevBtn = document.getElementById('gallery-prev');
        const nextBtn = document.getElementById('gallery-next');

        if (!slider || !prevBtn || !nextBtn) return;

        const getScrollAmount = () => Math.max(slider.clientWidth * 0.7, 240);

        const updateButtons = () => {
            const maxScroll = slider.scrollWidth - slider.clientWidth;
            const showPrev = slider.scrollLeft > 10;
            const showNext = slider.scrollLeft < maxScroll - 10;

            prevBtn.style.display = showPrev ? 'flex' : 'none';
            nextBtn.style.display = showNext ? 'flex' : 'none';
        };

        prevBtn.addEventListener('click', () => slider.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => slider.scrollBy({ left: getScrollAmount(), behavior: 'smooth' }));
        slider.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateButtons);

        setTimeout(updateButtons, 50);
        setTimeout(updateButtons, 250);
        setTimeout(updateButtons, 600);
    }

    function setupPriceCalculation(rate) {
        const checkIn = document.getElementById('check-in');
        const checkOut = document.getElementById('check-out');
        const calcArea = document.getElementById('price-calc');

        if (!checkIn || !checkOut || !calcArea) return;

        
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        checkIn.valueAsDate = today;
        checkOut.valueAsDate = tomorrow;

        function update() {
            const d1 = new Date(checkIn.value);
            const d2 = new Date(checkOut.value);
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
            
            const nights = diffDays;
            const subtotal = rate * nights;
            const fee = 20;
            const total = subtotal + fee;

            calcArea.innerHTML = `
                <div class="calc-row">
                    <span>$${rate} x ${nights} night${nights > 1 ? 's' : ''}</span>
                    <span>$${subtotal}</span>
                </div>
                <div class="calc-row">
                    <span>Service fee</span>
                    <span>$${fee}</span>
                </div>
                <div class="calc-total">
                    <span>Total</span>
                    <span>$${total}</span>
                </div>
            `;
        }

        checkIn.addEventListener('change', update);
        checkOut.addEventListener('change', update);
    }

   
    window.openLightbox = (url) => {
        let lightbox = document.getElementById('hotel-lightbox');
        if (!lightbox) {
            lightbox = document.createElement('div');
            lightbox.id = 'hotel-lightbox';
            lightbox.className = 'lightbox-overlay';
            lightbox.innerHTML = `
                <span class="lightbox-close">&times;</span>
                <img src="" alt="Lightbox">
            `;
            document.body.appendChild(lightbox);
            lightbox.querySelector('.lightbox-close').onclick = () => lightbox.classList.remove('active');
            lightbox.onclick = (e) => { if (e.target === lightbox) lightbox.classList.remove('active'); };
        }
        lightbox.querySelector('img').src = url;
        lightbox.classList.add('active');
    };

    async function init() {
        const urlParams = new URLSearchParams(window.location.search);
        const slug = urlParams.get('slug');
        const featuredContainer = document.getElementById('featured-container');

        if (hotelDetailsContent && slug) {
           
            const hotel = await fetchHotelBySlug(slug);
            renderHotelDetails(hotel);
        } else if (hotelsContainer) {
            
            allHotels = await fetchHotels();
            renderHotels(allHotels);

           
            if (featuredContainer) {
                const featured = allHotels.filter(h => h.category === 'luxury' || h.category === 'boutique').slice(0, 2);
                featuredContainer.innerHTML = featured.map(hotel => `
                    <div class="hotel-card featured reveal visible">
                        <div class="hotel-img-wrap featured-img-wrap">
                            <img src="${safeIsSubfolder && !hotel.image_url.startsWith('../') ? '../' + hotel.image_url : hotel.image_url}" alt="${hotel.name}">
                            <span class="hotel-badge hotel-badge-featured">Featured Selection</span>
                        </div>
                        <div class="hotel-content">
                            <h3 class="hotel-title hotel-title-featured">${hotel.name}</h3>
                            <p class="hotel-desc-featured">${hotel.location}</p>
                            <div class="hotel-booking">
                                <span class="hotel-price">$${hotel.price_per_night} <small>/night</small></span>
                                <a href="${basePath}php/hotel.php?slug=${hotel.slug}" class="btn-book">View Details <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            
            filterButtons.forEach(btn => {
                btn.addEventListener('click', async () => {
                    const filter = btn.dataset.filter;
                    filterButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const filtered = await fetchHotels(filter);
                    renderHotels(filtered);
                });
            });

           
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    const filtered = allHotels.filter(h => 
                        h.name.toLowerCase().includes(term) || 
                        h.location.toLowerCase().includes(term)
                    );
                    renderHotels(filtered);
                });
            }
        }
    }

    init();
});
