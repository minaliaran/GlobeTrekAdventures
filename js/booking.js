(function () {
   
    const radios = document.querySelectorAll('input[name="booking_type"]');
    const panels = document.querySelectorAll('.booking-type-panel[data-panel]');
    const chips = document.querySelectorAll('.booking-switcher .filter-chip');

    function showPanel(type) {
        panels.forEach(function (p) {
            const panel = p.getAttribute('data-panel');
           
            if (panel === 'payment') return;
            p.classList.toggle('active', panel === type);
        });
        chips.forEach(function (chip) {
            const radio = chip.querySelector('input[type="radio"]');
            chip.classList.toggle('active', radio && radio.value === type);
        });
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            showPanel(this.value);
            
            document.getElementById('payment-panel').style.display = 'none';
        });
    });

    
    document.getElementById('btn-next-to-payment').addEventListener('click', function () {
        const paymentPanel = document.getElementById('payment-panel');
        paymentPanel.style.display = 'block';
        paymentPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    
    const paymentSelect = document.getElementById('payment_method');
    const methodInfos = document.querySelectorAll('.payment-method-info');

    paymentSelect.addEventListener('change', function () {
        const method = this.value;
        methodInfos.forEach(el => el.classList.toggle('active', el.getAttribute('data-method') === method));
    });
})();