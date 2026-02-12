document.addEventListener('DOMContentLoaded', () => {
    // ANALYTICS: Track Page Visit
    const trackPageVisit = async () => {
        const path = window.location.pathname;
        let page = '';

        if (path.endsWith('index.html') || path.endsWith('/') || path.endsWith('forms/')) {
            page = 'index.html';
        } else if (path.endsWith('apply.html')) {
            page = 'apply.html';
        }

        if (page) {
            try {
                // Use beacon if available for reliability, or simple fetch
                // fetch is fine for page load
                await fetch(`track_visit.php?page=${page}`);
            } catch (e) {
                console.log('Analytics error', e);
            }
        }
    };
    trackPageVisit();

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
        const numAttendeesInput = document.getElementById('numAttendees');

        // Pre-populate Number of Attendees from localStorage if it exists
        const storedAttendees = localStorage.getItem('numAttendees');
        if (storedAttendees && numAttendeesInput) {
            numAttendeesInput.value = storedAttendees;
        }

        // SEARCH LOGIC
        const searchTypeRadios = document.getElementsByName('searchType');
        const searchLabel = document.getElementById('searchLabel');
        const searchResultsContainer = document.getElementById('searchResults');

        // Toggle Search Type
        searchTypeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'name') {
                    searchLabel.textContent = 'Student Name';
                    admInput.placeholder = 'e.g., John Kamau';
                } else {
                    searchLabel.textContent = 'Admission Number';
                    admInput.placeholder = 'e.g., 2025-06';
                }
                // Clear previous results/input
                admInput.value = '';
                searchResultsContainer.style.display = 'none';
                searchResultsContainer.innerHTML = '';
            });
        });

        // Search Function
        const performSearch = async () => {
            const term = admInput.value.trim();
            if (term.length < 3) return;

            // Determine type
            let type = 'adm';
            for (const r of searchTypeRadios) { if (r.checked) type = r.value; }

            try {
                admInput.parentElement.classList.add('loading');
                searchResultsContainer.style.display = 'none';

                const response = await fetch(`fetch_student.php?s_type=${type}&s_term=${encodeURIComponent(term)}`);
                const result = await response.json();

                if (result.success) {
                    // Check if array or single object
                    // API v2 returns data as array if multiple or object/array if single. 
                    // Let's normalize to array
                    let data = result.data;
                    if (!Array.isArray(data)) data = [data];

                    if (data.length === 1) {
                        populateStudentData(data[0]);
                    } else if (data.length > 1) {
                        showSearchResults(data);
                    }
                } else {
                    console.log('Backend message:', result.message || 'Student not found');
                    // Optional: Show "Not Found" message
                }
            } catch (error) {
                console.error('Error fetching student:', error);
            } finally {
                admInput.parentElement.classList.remove('loading');
            }
        };

        const showSearchResults = (students) => {
            searchResultsContainer.innerHTML = '';
            searchResultsContainer.style.display = 'block';

            // Inline Styles for the list
            searchResultsContainer.style.position = 'absolute';
            searchResultsContainer.style.top = '100%';
            searchResultsContainer.style.left = '0';
            searchResultsContainer.style.width = '100%';
            searchResultsContainer.style.maxHeight = '200px';
            searchResultsContainer.style.overflowY = 'auto';
            searchResultsContainer.style.background = 'white';
            searchResultsContainer.style.border = '1px solid #ccc';
            searchResultsContainer.style.zIndex = '1000';
            searchResultsContainer.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';

            const ul = document.createElement('ul');
            ul.style.listStyle = 'none';
            ul.style.padding = '0';
            ul.style.margin = '0';

            students.forEach(s => {
                const li = document.createElement('li');
                li.style.padding = '10px';
                li.style.borderBottom = '1px solid #eee';
                li.style.cursor = 'pointer';
                li.style.fontSize = '0.9rem';
                li.innerHTML = `<strong>${s.first_name} ${s.middle_name || ''} ${s.last_name}</strong> <span style="color:#666; font-size:0.8rem">(${s.admission_number})</span> ${s.already_applied ? '<span style="color: #d9534f; font-weight: bold; margin-left: 10px;">[ALREADY APPLIED]</span>' : ''}`;

                if (s.already_applied) {
                    li.style.opacity = '0.7';
                    li.style.cursor = 'not-allowed';
                    li.title = 'You have already submitted your application';
                }

                li.addEventListener('mouseenter', () => { if (!s.already_applied) li.style.background = '#f8fafc'; });
                li.addEventListener('mouseleave', () => li.style.background = 'white');

                li.addEventListener('click', () => {
                    if (s.already_applied) {
                        alert("Our records show that you have already submitted your graduation application.");
                        return;
                    }
                    populateStudentData(s);
                    searchResultsContainer.style.display = 'none';
                });

                ul.appendChild(li);
            });
            searchResultsContainer.appendChild(ul);
        };

        const populateStudentData = (data) => {
            if (data.already_applied) {
                alert("This admission number (" + data.admission_number + ") has already applied for graduation.");
                admInput.value = '';
                return;
            }
            if (firstNameInput) firstNameInput.value = data.first_name;
            if (middleNameInput) middleNameInput.value = data.middle_name || '';
            if (lastNameInput) lastNameInput.value = data.last_name;
            if (emailInput) emailInput.value = data.email;
            if (courseInput) courseInput.value = data.course;
            if (certLevelInput) certLevelInput.value = data.certificate_level;

            // Should we set the Adm No if they searched by name?
            // Yes, to ensure accuracy in submission
            if (admInput) admInput.value = data.admission_number;

            // Visual feedback
            [firstNameInput, middleNameInput, lastNameInput, emailInput, courseInput, certLevelInput].forEach(el => {
                if (el) {
                    el.style.borderColor = 'var(--success)';
                    el.classList.add('populated');
                    setTimeout(() => {
                        el.style.borderColor = '';
                        el.classList.remove('populated');
                    }, 1500);
                }
            });
        };

        // Trigger search on change or enter
        admInput.addEventListener('change', performSearch);
        admInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Handle Payment Status Radio Toggle
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const payRef = document.getElementById('paymentReference');
                const payDate = document.getElementById('paymentDate');

                const paymentInstructions = document.getElementById('paymentInstructions');

                if (e.target.value === 'paid') {
                    receiptSection.style.display = 'block';
                    if (paymentInstructions) paymentInstructions.style.display = 'none';
                    receiptInput.required = true;
                    if (payRef) payRef.required = true;
                    if (payDate) payDate.required = true;
                } else {
                    receiptSection.style.display = 'none';
                    if (paymentInstructions) paymentInstructions.style.display = 'block';
                    receiptInput.required = false;
                    if (payRef) payRef.required = false;
                    if (payDate) payDate.required = false;
                }
            });
        });

        // Handle Attendance Mode (Select)
        const attendanceSelect = document.getElementById('attendanceMode'); // Now a Select
        const attendeesInputGroup = document.getElementById('attendeesInputGroup');
        const guestListSection = document.getElementById('guestListSection');
        const guestListInput = document.getElementById('guestList');

        const attendanceReasonSection = document.getElementById('attendanceReasonSection');
        const attendanceReasonInput = document.getElementById('attendanceReason');

        const toggleAttendanceFields = () => {
            const mode = attendanceSelect.value;
            const attendeesCount = parseInt(numAttendeesInput.value) || 0;

            // 1. Show/Hide Attendees Input (Only for Physical)
            if (mode === 'Physical') {
                if (attendeesInputGroup) attendeesInputGroup.style.display = 'block';
                if (numAttendeesInput) numAttendeesInput.required = true;

                // Hide reason
                if (attendanceReasonSection) attendanceReasonSection.style.display = 'none';
                if (attendanceReasonInput) attendanceReasonInput.required = false;

            } else {
                // Online or Absentia
                if (attendeesInputGroup) attendeesInputGroup.style.display = 'none';
                if (numAttendeesInput) {
                    numAttendeesInput.required = false;
                    numAttendeesInput.value = 0; // Reset count
                }

                // Show Reason
                if (attendanceReasonSection) attendanceReasonSection.style.display = 'block';
                if (attendanceReasonInput) attendanceReasonInput.required = true;
            }

            // 2. Show/Hide Guest List Upload
            // Condition: Physical AND Attendees > 0
            if (mode === 'Physical' && attendeesCount > 0) {
                if (guestListSection) guestListSection.style.display = 'block';
                if (guestListInput) guestListInput.required = true;
            } else {
                if (guestListSection) guestListSection.style.display = 'none';
                if (guestListInput) guestListInput.required = false;
            }
        };

        if (attendanceSelect) {
            attendanceSelect.addEventListener('change', toggleAttendanceFields);
        }

        if (numAttendeesInput) {
            numAttendeesInput.addEventListener('input', toggleAttendanceFields);
        }

        // Run once on load to set initial state
        // Run once on load
        toggleAttendanceFields();

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
            const btn = form.querySelector('.submit-btn');
            btn.textContent = 'Submitting...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
        });
    }
});
