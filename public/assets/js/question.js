const letters   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
let altIndex    = document.querySelectorAll('#alternatives-wrapper .alternative-row').length;

document.getElementById('add-alternative').addEventListener('click', function () {
    const wrapper = document.getElementById('alternatives-wrapper');

    if (altIndex >= letters.length) {
        Swal.fire({
            icon: 'info',
            title: 'Limite atingido',
            text: 'Você já adicionou todas as 26 alternativas possíveis (A a Z).',
            confirmButtonText: 'Certo!',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
        return;
    }

    const newRow = document.createElement('div');
    newRow.className = 'row mt-2 alternative-row';

    newRow.innerHTML = `
        <div class="col-12 col-sm-12 col-md-8 col-lg-8">
            <div class="form-floating form-floating-outline mb-2">
                <input type="text" class="form-control" name="alternative[]" placeholder="Ex: alternativa ${letters[altIndex]}">
                <label>${letters[altIndex]})</label>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-center">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input correct-switch" type="checkbox" name="correct[]" value="${altIndex}">
                <label class="form-check-label ms-2">Correta</label>
            </div>
        </div> 
    `;

    wrapper.appendChild(newRow);
    altIndex++;
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('correct-switch')) {
        const allSwitches = document.querySelectorAll('.correct-switch');
        allSwitches.forEach(sw => {
            if (sw !== e.target) sw.checked = false;
        });
    }
});

document.getElementById('question-form').addEventListener('submit', function (e) {

    tinymce.triggerSave();
    
    const alternatives  = document.querySelectorAll('input[name="alternative[]"]');
    const corrects      = document.querySelectorAll('.correct-switch:checked');
    const title         = document.getElementById('question').value;

    if (title === '') {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Questão obrigatória',
            text: 'É necessário informar um texto para a questão!',
            confirmButtonText: 'Ok',
            customClass: {
                confirmButton: 'btn btn-warning'
            },
            buttonsStyling: false
        });
        return;
    }

    let filledAlternatives = 0;
    alternatives.forEach(input => {
        if (input.value.trim() !== '') filledAlternatives++;
    });

    if (filledAlternatives < 2) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Poucas alternativas',
            text: 'Informe no mínimo duas alternativas para a questão!',
            confirmButtonText: 'Ok',
            customClass: {
                confirmButton: 'btn btn-info'
            },
            buttonsStyling: false
        });
        return;
    }

    if (corrects.length !== 1) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Alternativa correta',
            text: 'Selecione exatamente uma alternativa como correta!',
            confirmButtonText: 'Entendi',
            customClass: {
                confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
        return;
    }
});

