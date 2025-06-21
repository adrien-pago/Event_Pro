document.addEventListener('DOMContentLoaded', function() {
    const isFullDay = document.getElementById('event_isFullDay');
    
    // S'arrête si on n'est pas sur une page avec le formulaire d'événement
    if (!isFullDay) {
        return;
    }

    const timeFields = document.getElementById('time-fields');
    const startTime = document.getElementById('event_startTime');
    const endTime = document.getElementById('event_endTime');

    function toggleTimeFields() {
        if (isFullDay.checked) {
            timeFields.style.display = 'none';
            if (startTime) startTime.value = '00:00';
            if (endTime) endTime.value = '23:59';
        } else {
            timeFields.style.display = '';
            // On ne vide pas les champs pour que l'utilisateur puisse
            // facilement revenir en arrière après un clic accidentel.
        }
    }
    
    isFullDay.addEventListener('change', toggleTimeFields);
    // On vérifie l'état initial au chargement de la page
    toggleTimeFields();
    
    // Spinner pour le bouton de soumission
    const form = isFullDay.closest('form');
    if (form) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            form.addEventListener('submit', function(e) {
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Enregistrement...`;
            });
        }
    }
}); 