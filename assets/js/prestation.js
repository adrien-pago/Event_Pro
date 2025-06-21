// assets/js/prestation.js

// Ce fichier est prêt pour le JavaScript spécifique à la page des prestations.
// Par exemple, pour les futures modales d'ajout/modification. 

// Gestion des prestations
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = document.querySelector('[data-page]')?.getAttribute('data-page');
    
    // Gestion de la liste des prestations
    if (currentPage === 'prestation-index') {
        initPrestationList();
    }
    
    // Gestion du formulaire de modification de prestation
    if (currentPage === 'prestation-edit') {
        initPrestationForm();
    }
});

// Initialisation de la liste des prestations
function initPrestationList() {
    // Gestion de la modale de modification
    const editModal = new bootstrap.Modal(document.getElementById('editPrestationModal'));
    const editModalContent = document.getElementById('editPrestationModalContent');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const url = this.dataset.url;

            // Affiche un spinner de chargement
            editModalContent.innerHTML = `
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `;
            editModal.show();

            // Requête AJAX pour récupérer le formulaire
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {
                    editModalContent.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du formulaire:', error);
                    editModalContent.innerHTML = '<div class="modal-body"><p class="text-danger">Impossible de charger le formulaire.</p></div>';
                });
        });
    });
    
    // Gestion de la suppression de prestations
    const deleteButtons = document.querySelectorAll('.delete-prestation-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const prestationId = this.dataset.prestationId;
            const prestationName = this.dataset.prestationName;
            const csrfToken = this.dataset.csrfToken;
            const eventId = document.querySelector('[data-event-id]').dataset.eventId;
            
            // Afficher la modal de confirmation
            document.getElementById('prestationName').textContent = prestationName;
            document.getElementById('deleteForm').action = `/event/${eventId}/prestations/${prestationId}/delete`;
            document.getElementById('deleteToken').value = csrfToken;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deletePrestationModal'));
            deleteModal.show();
        });
    });
}

// Initialisation du formulaire de prestation
function initPrestationForm() {
    // Spinner pour le bouton de soumission
    const form = document.querySelector('form');
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