document.addEventListener('DOMContentLoaded', () => {
    // IMAGE SLIDER LOGIC
    const sliderTrack = document.getElementById('sliderTrack');
    const nextBtn = document.getElementById('nextSlide');
    const prevBtn = document.getElementById('prevSlide');

    if (sliderTrack) {
        const slides = document.querySelectorAll('.slide');
        const firstClone = slides[0].cloneNode(true);
        const lastClone = slides[slides.length - 1].cloneNode(true);

        firstClone.classList.remove('active');
        lastClone.classList.remove('active');

        sliderTrack.appendChild(firstClone);
        sliderTrack.insertBefore(lastClone, slides[0]);

        const allSlides = document.querySelectorAll('.slide');
        let currentSlide = 1; // Start at the first real slide
        let isTransitioning = false;

        const updateSlider = (smooth = true) => {
            const slideWidth = 85;
            const offset = (100 - slideWidth) / 2;

            sliderTrack.style.transition = smooth ? 'transform 0.5s cubic-bezier(0.23, 1, 0.32, 1)' : 'none';
            sliderTrack.style.transform = `translateX(calc(-${currentSlide * slideWidth}% - ${currentSlide * 20}px + ${offset}%))`;

            // Toggle active class (using modulo to find index of real slides)
            const realIndex = (currentSlide - 1 + slides.length) % slides.length;
            allSlides.forEach((s, i) => {
                const isRealActive = (i === currentSlide);
                s.classList.toggle('active', isRealActive);
            });
        };

        // Initial position
        updateSlider(false);

        const nextSlide = () => {
            if (isTransitioning) return;
            isTransitioning = true;
            currentSlide++;
            updateSlider();
        };

        const prevSlide = () => {
            if (isTransitioning) return;
            isTransitioning = true;
            currentSlide--;
            updateSlider();
        };

        sliderTrack.addEventListener('transitionend', () => {
            isTransitioning = false;
            // Jump to real slide if we are on a clone
            if (currentSlide === allSlides.length - 1) {
                currentSlide = 1;
                updateSlider(false);
            } else if (currentSlide === 0) {
                currentSlide = allSlides.length - 2;
                updateSlider(false);
            }
        });

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto slide - reduced to 3 seconds for faster movement
        let autoSlide = setInterval(nextSlide, 3000);

        // Pause on hover
        const sliderContainer = document.querySelector('.hero-slider');
        sliderContainer.addEventListener('mouseenter', () => clearInterval(autoSlide));
        sliderContainer.addEventListener('mouseleave', () => autoSlide = setInterval(nextSlide, 3000));
    }

    // GLITTER EFFECT
    const glitterContainer = document.getElementById('glitterContainer');
    if (glitterContainer) {
        const colors = ['#ffffff', '#ffd700', '#c9a227', '#1a4a8e', '#2563eb', '#f0f0f0', '#ffeb3b'];
        for (let i = 0; i < 100; i++) {
            const glitter = document.createElement('div');
            glitter.className = 'glitter';

            // Random position across whole viewport
            glitter.style.left = Math.random() * 100 + 'vw';
            glitter.style.top = Math.random() * 100 + 'vh';

            // Random color
            glitter.style.background = colors[Math.floor(Math.random() * colors.length)];

            // Random timing
            glitter.style.animationDelay = Math.random() * 5 + 's';
            glitter.style.animationDuration = (Math.random() * 2 + 2) + 's';

            // Random size
            const size = Math.random() * 5 + 3 + 'px';
            glitter.style.width = size;
            glitter.style.height = size;

            glitterContainer.appendChild(glitter);
        }
    }

    // CAP INTRO & FLOATING CAPS
    const introOverlay = document.getElementById('capIntroOverlay');
    const capsContainer = document.getElementById('floatingCapsContainer');

    if (introOverlay) {
        // Auto-dissolve intro after 4 seconds
        setTimeout(() => {
            introOverlay.style.opacity = '0';
            setTimeout(() => {
                introOverlay.style.visibility = 'hidden';
            }, 1000);
        }, 4000);
    }

    if (capsContainer) {
        function createCap() {
            const cap = document.createElement('div');
            cap.className = 'mortar-cap';
            cap.textContent = '🎓';

            cap.style.left = Math.random() * 95 + 'vw';

            // Random duration and delay
            const duration = Math.random() * 4 + 6 + 's';
            cap.style.animationDuration = duration;
            cap.style.animationDelay = Math.random() * 5 + 's';

            // Initial random rotation
            cap.style.transform = `rotate(${Math.random() * 360}deg)`;

            capsContainer.appendChild(cap);

            // Cleanup after animation
            setTimeout(() => {
                cap.remove();
                createCap(); // Replace it
            }, parseFloat(duration) * 1000);
        }

        // Initial set of caps
        for (let i = 0; i < 15; i++) {
            setTimeout(createCap, i * 400);
        }
    }

    // DIPLOMA SCROLL ANIMATION
    const scrollEl = document.getElementById('graduationScroll');
    if (scrollEl) {
        const triggerAnimation = () => {
            // Step 1: Slide in
            setTimeout(() => {
                scrollEl.classList.add('visible');

                // Step 2: Untie ribbon
                setTimeout(() => {
                    scrollEl.classList.add('untied');

                    // Step 3: Roll open
                    setTimeout(() => {
                        scrollEl.classList.add('open');
                    }, 800);
                }, 1500);
            }, 1000);
        };

        // Trigger after the cap intro overlay is mostly gone
        setTimeout(triggerAnimation, 3500);
    }

    // FORM LOGIC (ONLY IF FORM EXISTS)
    const form = document.getElementById('graduationForm');
    if (form) {
        const fileInput = document.getElementById('paymentReceipt');
        const fileDisplay = document.querySelector('.file-upload-display span');
        const admInput = document.getElementById('admissionNumber');
        const firstNameInput = document.getElementById('firstName');
        const middleNameInput = document.getElementById('middleName');
        const lastNameInput = document.getElementById('lastName');
        const emailInput = document.getElementById('email');
        const courseInput = document.getElementById('course');
        const certLevelInput = document.getElementById('certificateLevel');
        const paymentRadios = document.getElementsByName('paymentStatus');
        const receiptSection = document.getElementById('receiptSection');
        const receiptInput = document.getElementById('paymentReceipt');

        // Handle Admission Number lookup
        admInput.addEventListener('change', async () => {
            const adm = admInput.value.trim();
            if (adm.length < 3) return;

            try {
                admInput.parentElement.classList.add('loading');
                const response = await fetch(`fetch_student.php?admission_number=${encodeURIComponent(adm)}`);
                const result = await response.json();

                if (result.success) {
                    firstNameInput.value = result.data.first_name;
                    middleNameInput.value = result.data.middle_name || '';
                    lastNameInput.value = result.data.last_name;
                    emailInput.value = result.data.email;
                    courseInput.value = result.data.course;
                    certLevelInput.value = result.data.certificate_level;

                    // Visual feedback
                    [firstNameInput, middleNameInput, lastNameInput, emailInput, courseInput, certLevelInput].forEach(el => {
                        el.style.borderColor = 'var(--success)';
                        setTimeout(() => el.style.borderColor = '', 1000);
                    });
                } else {
                    console.log('Student not found. Manual registration enabled.');
                }
            } catch (error) {
                console.error('Error fetching student:', error);
            } finally {
                admInput.parentElement.classList.remove('loading');
            }
        });

        // Handle Payment Status Radio Toggle
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'paid') {
                    receiptSection.style.display = 'block';
                    receiptInput.required = true;
                } else {
                    receiptSection.style.display = 'none';
                    receiptInput.required = false;
                }
            });
        });

        // Handle file selection display and preview
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const fileName = file ? file.name : 'Choose a file or drag it here';
            const previewContainer = document.getElementById('filePreview');
            const previewImage = document.getElementById('previewImage');
            const pdfIcon = document.getElementById('pdfIcon');

            fileDisplay.textContent = fileName;

            if (file) {
                fileDisplay.parentElement.style.borderColor = 'var(--success)';
                fileDisplay.parentElement.parentElement.style.borderColor = 'var(--success)';

                // Preview logic
                previewContainer.style.display = 'block';
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        previewImage.src = event.target.result;
                        previewImage.style.display = 'block';
                        pdfIcon.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    previewImage.style.display = 'none';
                    pdfIcon.style.display = 'block';
                }
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Validation
        form.addEventListener('submit', (e) => {
            const emailInputVal = document.getElementById('email').value;
            if (!emailInputVal.endsWith('@kisecollege.ac.ke') && !emailInputVal.includes('test')) {
                if (!confirm('You are using a non-institutional email. Proceed anyway?')) {
                    e.preventDefault();
                    return;
                }
            }
            const btn = form.querySelector('.submit-btn');
            btn.textContent = 'Submitting...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
        });
    }
});
