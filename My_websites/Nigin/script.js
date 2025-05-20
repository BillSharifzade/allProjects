document.addEventListener('DOMContentLoaded', function() {
    // Preloader
    const preloader = document.querySelector('.preloader');
    window.addEventListener('load', function() {
        preloader.classList.add('hidden');
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 500);
        
        animateHeroElements();
    });

    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('nav ul');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (mobileMenuBtn && mobileMenuBtn.classList.contains('active')) {
                mobileMenuBtn.classList.remove('active');
                navMenu.classList.remove('active');
            }
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Offset for header
                    behavior: 'smooth'
                });
            }
        });
    });

    // Gold Calculator Logic
    const goldTypeSelect = document.getElementById('gold-type');
    const goldWeightInput = document.getElementById('gold-weight');
    const calculateBtn = document.getElementById('calculate-btn');
    const loanAmountElement = document.getElementById('loan-amount');
    
    // Base prices per gram for each gold type (can be updated dynamically)
    const goldPrices = {
        '375': 200, // 9K gold price per gram in rubles
        '585': 460, // 14K gold price per gram in rubles
        '750': 590, // 18K gold price per gram in rubles
        '875': 645  // 21K gold price per gram in rubles
    };
    
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateLoanAmount);
    }
    
    // Gold Calculator Logic
    function calculateLoanAmount() {
        const goldType = goldTypeSelect.value;
        const goldWeight = parseFloat(goldWeightInput.value);
        
        if (isNaN(goldWeight) || goldWeight <= 0) {
            alert('Пожалуйста, введите корректный вес золота');
            return;
        }
        
        // Calculate loan amount based on gold type and weight
        const pricePerGram = goldPrices[goldType];
        const loanAmount = pricePerGram * goldWeight;
        
        // Format the loan amount with thousand separators
        const formattedAmount = new Intl.NumberFormat('ru-RU').format(Math.round(loanAmount));
        
        // Animate the result - fixed to use the suffix parameter
        animateValue(loanAmountElement, 0, Math.round(loanAmount), 1000, ' Сомони');
    }
    
    // Email Form Submission
    const emailForm = document.getElementById('email-form');
    
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            
            // Here you would typically send the data to a server
            // For now, we'll just show a success message
            
            // Create a success message element
            const successMessage = document.createElement('div');
            successMessage.className = 'success-message';
            successMessage.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <p>Спасибо, ${name}! Ваше сообщение успешно отправлено. Мы свяжемся с вами в ближайшее время.</p>
            `;
            
            // Replace the form with the success message
            emailForm.innerHTML = '';
            emailForm.appendChild(successMessage);
        });
    }
    
    // Hero Section Animations
    function animateHeroElements() {
        const heroTexts = document.querySelectorAll('.animate-text');
        const heroBtn = document.querySelector('.animate-btn');
        const heroImages = document.querySelectorAll('.animate-image');
        
        // Animate texts with delay
        heroTexts.forEach((text, index) => {
            setTimeout(() => {
                text.style.opacity = '1';
                text.style.transform = 'translateY(0)';
            }, 300 * index);
        });
        
        // Animate button
        setTimeout(() => {
            if (heroBtn) {
                heroBtn.style.opacity = '1';
                heroBtn.style.transform = 'translateY(0)';
            }
        }, heroTexts.length * 300);
        
        // Animate images
        heroImages.forEach((img, index) => {
            setTimeout(() => {
                img.style.opacity = '1';
                img.style.transform = 'translateY(0) rotate(' + (index === 0 ? '-15deg' : '0') + ')';
            }, (heroTexts.length * 300) + 200 + (index * 200));
        });
    }
    
    // Scroll animations for service cards
    const serviceCards = document.querySelectorAll('.service-card');
    
    function checkScroll() {
        serviceCards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (cardTop < windowHeight * 0.8) {
                card.classList.add('visible');
            }
        });
    }
    
    // Check for visible elements on page load
    window.addEventListener('load', checkScroll);
    
    // Check for visible elements on scroll
    window.addEventListener('scroll', checkScroll);
});

// Tab Switching Logic
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

if (tabBtns.length > 0) {
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-calculator').classList.add('active');
        });
    });
}

// Annuity Calculator Logic
const annuityAmountInput = document.getElementById('annuity-amount');
const annuityTermSelect = document.getElementById('annuity-term');
const calculateAnnuityBtn = document.getElementById('calculate-annuity-btn');
const monthlyPaymentElement = document.getElementById('monthly-payment');
const paymentScheduleTable = document.getElementById('payment-schedule-table');
const effectiveRateElement = document.getElementById('annuity-effective-rate');

if (calculateAnnuityBtn) {
    calculateAnnuityBtn.addEventListener('click', calculateAnnuityPayment);
}

function calculateAnnuityPayment() {
    const loanAmount = parseFloat(annuityAmountInput.value);
    const loanTerm = parseInt(annuityTermSelect.value);
    
    if (isNaN(loanAmount) || loanAmount <= 0) {
        alert('Пожалуйста, введите корректную сумму кредита');
        return;
    }
    
    // Determine interest rate based on loan amount
    let interestRate; // This is treated as a monthly rate in the example's formula
    
    if (loanAmount < 2000) {
        interestRate = 7.1; // 7.1% monthly
    } else if (loanAmount < 10000) {
        interestRate = 6.5; // 6.5% monthly
    } else if (loanAmount < 20000) {
        interestRate = 6.0; // 6% monthly
    } else {
        interestRate = 5.1; // 5.1% monthly
    }
    
    // Flawed monthly rate (as used in example logic leading to desired effective rate)
    const monthlyInterestRate_flawed = interestRate / 100; 
    
    // Calculate monthly payment using annuity formula with the flawed rate
    const x = Math.pow(1 + monthlyInterestRate_flawed, loanTerm);
    const monthlyPayment = loanAmount * monthlyInterestRate_flawed * x / (x - 1);
    
    // --- Custom Effective Rate Calculation ---
    // 1. Calculate Total Interest Paid (based on the calculated monthly payment)
    const totalInterestPaid = (monthlyPayment * loanTerm) - loanAmount;
    
    // 2. Calculate Average Monthly Interest
    const averageMonthlyInterest = totalInterestPaid / loanTerm;
    
    // 3. Calculate Custom Effective Rate (%)
    const customEffectiveRate = (averageMonthlyInterest / loanAmount) * 100;
    const formattedCustomEffectiveRate = customEffectiveRate.toFixed(2); // Format to 2 decimal places
    
    // Animate the monthly payment result
    animateValue(monthlyPaymentElement, 0, Math.round(monthlyPayment), 1000, ' Сомони');
    
    // Update effective rate display with the custom calculated value
    if (effectiveRateElement) {
        // Update the text value directly
        effectiveRateElement.innerHTML = `<span style="font-weight:bold;">${formattedCustomEffectiveRate}%</span>`;
        
        // Remove previous visualization if exists (as it's no longer relevant)
        const existingVisualization = document.getElementById('rate-visualization');
        if (existingVisualization) {
            existingVisualization.remove();
        }
        
        // Optional: Style the result span if needed (e.g., color)
        const resultSpan = effectiveRateElement.querySelector('span');
        if (resultSpan) {
            resultSpan.style.color = '#e30613'; // Example: use primary color
        }
    }
    
    // Generate payment schedule using the monthly payment and flawed rate 
    // for consistency with the total interest calculation derived from the example.
    generatePaymentSchedule(loanAmount, monthlyPayment, monthlyInterestRate_flawed, loanTerm);
}

function generatePaymentSchedule(loanAmount, monthlyPayment, monthlyInterestRate, loanTerm) {
    // Clear previous table rows
    const tableBody = paymentScheduleTable.querySelector('tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    let remainingBalance = loanAmount;
    
    for (let month = 1; month <= loanTerm; month++) {
        // Calculate interest for this month
        const interestPayment = remainingBalance * monthlyInterestRate;
        
        // Calculate principal for this month
        const principalPayment = monthlyPayment - interestPayment;
        
        // Update remaining balance
        remainingBalance -= principalPayment;
        
        // Create table row
        const row = document.createElement('tr');
        
        // Format numbers
        const formattedMonthlyPayment = new Intl.NumberFormat('ru-RU').format(Math.round(monthlyPayment));
        const formattedPrincipalPayment = new Intl.NumberFormat('ru-RU').format(Math.round(principalPayment));
        const formattedInterestPayment = new Intl.NumberFormat('ru-RU').format(Math.round(interestPayment));
        const formattedRemainingBalance = new Intl.NumberFormat('ru-RU').format(Math.max(0, Math.round(remainingBalance)));
        
        // Add cells to row
        row.innerHTML = `
            <td>${month}</td>
            <td>${formattedMonthlyPayment} Сомони</td>
            <td>${formattedPrincipalPayment} Сомони</td>
            <td>${formattedInterestPayment} Сомони</td>
            <td>${formattedRemainingBalance} Сомони</td>
        `;
        
        // Add row to table
        tableBody.appendChild(row);
    }
}

// Fix the original animateValue function to use the suffix parameter correctly
function animateValue(element, start, end, duration, suffix = ' Сомони') {
    element.style.transform = 'scale(0.8)';
    element.style.opacity = '0.5';
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = new Intl.NumberFormat('ru-RU').format(value) + suffix;
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            element.style.transform = 'scale(1.1)';
            element.style.opacity = '1';
            
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
    };
    window.requestAnimationFrame(step);
}