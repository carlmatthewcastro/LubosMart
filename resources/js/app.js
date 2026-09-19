// ===== PH ADDRESS CASCADING DROPDOWNS (Province -> City/Municipality -> Barangay) =====
// Uses the free public PSGC API: https://psgc.gitlab.io/api/
// Auto-applies to any form on the page that has selects named
// "province", "municipality", and "barangay".
(function () {
    const API = 'https://psgc.gitlab.io/api';

    async function fetchJSON(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error('Failed to fetch ' + url);
        return res.json();
    }

    function fillSelect(select, items, placeholder) {
        select.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.appendChild(opt);

        items
            .slice()
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(function (item) {
                const o = document.createElement('option');
                o.value = item.name;
                o.dataset.code = item.code;
                o.textContent = item.name;
                select.appendChild(o);
            });

        select.disabled = false;
    }

    function resetSelect(select, placeholder) {
        select.innerHTML = '<option value="">' + placeholder + '</option>';
        select.disabled = true;
    }

    async function initAddressForm(form) {
        const provinceSelect = form.querySelector('select[name="province"]');
        const municipalitySelect = form.querySelector('select[name="municipality"]');
        const barangaySelect = form.querySelector('select[name="barangay"]');

        if (!provinceSelect || !municipalitySelect || !barangaySelect) return;

        resetSelect(municipalitySelect, 'Select province first');
        resetSelect(barangaySelect, 'Select city/municipality first');

        try {
            const provinces = await fetchJSON(API + '/provinces/');
            fillSelect(provinceSelect, provinces, 'Select Province');
        } catch (e) {
            provinceSelect.innerHTML = '<option value="">Unable to load provinces</option>';
        }

        provinceSelect.addEventListener('change', async function () {
            resetSelect(municipalitySelect, 'Loading...');
            resetSelect(barangaySelect, 'Select city/municipality first');

            const selected = provinceSelect.options[provinceSelect.selectedIndex];
            const code = selected ? selected.dataset.code : null;
            if (!code) { resetSelect(municipalitySelect, 'Select province first'); return; }

            try {
                const cities = await fetchJSON(API + '/provinces/' + code + '/cities-municipalities/');
                fillSelect(municipalitySelect, cities, 'Select City/Municipality');
            } catch (e) {
                municipalitySelect.innerHTML = '<option value="">Unable to load</option>';
            }
        });

        municipalitySelect.addEventListener('change', async function () {
            resetSelect(barangaySelect, 'Loading...');

            const selected = municipalitySelect.options[municipalitySelect.selectedIndex];
            const code = selected ? selected.dataset.code : null;
            if (!code) { resetSelect(barangaySelect, 'Select city/municipality first'); return; }

            try {
                const barangays = await fetchJSON(API + '/cities-municipalities/' + code + '/barangays/');
                fillSelect(barangaySelect, barangays, 'Select Barangay');
            } catch (e) {
                barangaySelect.innerHTML = '<option value="">Unable to load</option>';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(initAddressForm);
    });
})();