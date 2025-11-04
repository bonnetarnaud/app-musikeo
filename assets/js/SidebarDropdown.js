/**
 * Classe pour gérer les dropdowns de la sidebar
 */
export default class SidebarDropdown {
    constructor() {
        this.init();
    }

    init() {
        // Attendre que le DOM soit chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        const trigger = document.querySelector('.sidebar-header-switcher [role="button"]');
        const dropdown = document.querySelector('.dropdown');

        if (trigger && dropdown) {
            // Gérer le clic sur le trigger
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Fermer le dropdown quand on clique ailleurs
            document.addEventListener('click', (event) => {
                if (!dropdown.contains(event.target) && !trigger.contains(event.target)) {
                    this.closeDropdown();
                }
            });
        }

        // Gérer les sous-menus
        this.setupSubmenuEventListeners();
    }

    setupSubmenuEventListeners() {
        const submenuToggles = document.querySelectorAll('[data-submenu-toggle]');
        
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const targetId = toggle.getAttribute('data-submenu-toggle');
                const submenu = toggle.closest('.nav-submenu');
                
                if (submenu) {
                    this.toggleSubmenu(submenu);
                }
            });
        });
    }

    toggleSubmenu(submenu) {
        const isActive = submenu.classList.contains('active');
        
        // Fermer tous les autres sous-menus
        document.querySelectorAll('.nav-submenu.active').forEach(otherSubmenu => {
            if (otherSubmenu !== submenu) {
                otherSubmenu.classList.remove('active');
            }
        });
        
        // Toggle le sous-menu actuel
        submenu.classList.toggle('active');
    }

    toggleDropdown() {
        const dropdown = document.querySelector('.dropdown');
        const arrow = document.querySelector('.sidebar-header-switcher svg');
        
        if (dropdown) {
            dropdown.classList.toggle('active');
            
            // Animation de la flèche
            if (arrow) {
                const isActive = dropdown.classList.contains('active');
                arrow.style.transform = isActive ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
    }

    closeDropdown() {
        const dropdown = document.querySelector('.dropdown');
        const arrow = document.querySelector('.sidebar-header-switcher svg');
        
        if (dropdown) {
            dropdown.classList.remove('active');
            
            if (arrow) {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    }
}