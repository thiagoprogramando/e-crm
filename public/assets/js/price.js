document.querySelectorAll('form.fee-form').forEach(form => {
    let clickedButton = null;

    form.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function () {
            clickedButton = this.value;
        });
    });

    form.addEventListener('submit', function (e) {
        if (clickedButton !== 'save') return;

        let valid = true;

        this.querySelectorAll('input.fee-value:not(:disabled)').forEach(input => {
            const value = parseFloat(input.value);
            const min = parseFloat(input.dataset.min || 0);
            const max = parseFloat(input.dataset.max || Infinity);
            const label = input.dataset.label;

            if (input.value === '') {
                Swal.fire('Campo obrigatório', `Informe um valor para "${label}"`, 'warning');
                valid = false;
                e.preventDefault();
                return;
            }

            if (value < min) {
                Swal.fire('Valor abaixo do mínimo', `O valor de "${label}" deve ser no mínimo ${min}`, 'warning');
                valid = false;
                e.preventDefault();
                return;
            }

            if (value > max) {
                Swal.fire('Valor acima do máximo', `O valor de "${label}" deve ser no máximo ${max}`, 'warning');
                valid = false;
                e.preventDefault();
                return;
            }
        });

        if (!valid) e.preventDefault();
    });
});

function confirmRemoveService(button) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Deseja remover este serviço da cotação?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, remover',
        confirmButtonColor: '#72E128',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {

            const form = button.closest('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'delete';
            form.appendChild(input);
            form.submit();
        }
    });
}

function confirm(button) {
    Swal.fire({
        title: 'Confirmar?',
        text: 'Ao confirmar, os dados serão excluídos permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, continuar',
        confirmButtonColor: '#72E128',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {

            const form = button.closest('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'clean';
            form.appendChild(input);
            form.submit();
        }
    });
}