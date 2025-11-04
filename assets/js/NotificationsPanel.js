export class NotificationsPanel {
    constructor() {
        this.panel = document.getElementById('notifications-panel');
        this.overlay = document.getElementById('notifications-overlay');
        this.isOpen = false;
        
        // Vérifier que les éléments existent
        if (!this.panel || !this.overlay) {
            console.warn('NotificationsPanel: Éléments requis non trouvés');
            return;
        }
        
        this.init();
    }
    
    init() {
        // Écouter les clics sur l'overlay pour fermer
        this.overlay.addEventListener('click', () => this.close());
        
        // Fermer avec la touche Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Empêcher la fermeture quand on clique dans le panneau
        this.panel.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        this.isOpen = true;
        this.panel.classList.add('open');
        this.overlay.classList.add('open');
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
        
        // Focus sur le panneau pour l'accessibilité
        this.panel.setAttribute('tabindex', '-1');
        this.panel.focus();
    }
    
    close() {
        this.isOpen = false;
        this.panel.classList.remove('open');
        this.overlay.classList.remove('open');
        
        // Rétablir le scroll du body
        document.body.style.overflow = '';
        
        // Retirer le focus
        this.panel.removeAttribute('tabindex');
    }
}

// Instance globale pour la fonction toggle
let notificationsPanel;

// Initialiser quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    notificationsPanel = new NotificationsPanel();
});

// Fonction globale pour le onclick dans le template
window.toggleNotifications = () => {
    if (notificationsPanel) {
        notificationsPanel.toggle();
    }
};