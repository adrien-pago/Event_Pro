// Gestion des événements
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = document.querySelector('[data-page]')?.getAttribute('data-page');
    
    // Gestion des formulaires d'événements (new et edit)
    if (currentPage === 'event-new' || currentPage === 'event-edit') {
        initEventForm();
    }
    
    // Gestion de la liste des événements
    if (currentPage === 'event-index') {
        initEventList();
    }
});

// Initialisation du formulaire d'événement
function initEventForm() {
    const isFullDay = document.getElementById('event_isFullDay');
    
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
        }
    }
    
    isFullDay.addEventListener('change', toggleTimeFields);
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
}

// Initialisation de la liste des événements
function initEventList() {
    // Gestion des clics sur les lignes d'événements
    const rows = document.querySelectorAll('.event-row');
    rows.forEach(row => {
        row.addEventListener('click', function() {
            window.location.href = this.dataset.url;
        });

        const interactiveElements = row.querySelectorAll('a, button');
        interactiveElements.forEach(el => {
            el.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
    
    // Gestion de la suppression d'événements
    const deleteButtons = document.querySelectorAll('.delete-event-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const eventId = this.dataset.eventId;
            const eventName = this.dataset.eventName;
            
            // Afficher la modal de confirmation
            document.getElementById('eventName').textContent = eventName;
            document.getElementById('deleteForm').action = `/events/${eventId}/delete`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });
} 