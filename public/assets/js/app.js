$(document).on('change', '#product_id', function () {
    let selected = this.options[this.selectedIndex];
    let options = selected.getAttribute('data-options');

    if (!options) {
        $('#product-options-container').html('');
        return;
    }

    options = JSON.parse(options);

    console.log("OPTIONS:", options);

    let html = `<div class="row g-2">`;

    options.forEach(option => {
        html += `
            <div class="col-sm-6 col-md-6 col-lg-6 mb-md-0 mb-2">
                <div class="form-check custom-option custom-option-basic">
                    <label class="form-check-label custom-option-content" for="option${option.uuid}">
                        <input name="product_option_id" class="form-check-input" type="radio" value="${option.uuid}" id="option${option.uuid}" required>
                        <span class="custom-option-header">
                            <span class="h6 mb-0">${option.title}</span>
                        </span>
                        <span class="custom-option-body">
                            <small>${option.description ?? ''}</small>
                        </span>
                    </label>
                </div>
            </div>
        `;
    });

    html += `</div>`;

    $('#product-options-container').html(html);
});