/*import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        urls: Array,
        statusId: String  
    }

    connect() {
        console.log('Modal Cycle Controller е свързан.');
        console.log('URLs:', this.urlsValue);
        console.log('Status ID:', this.statusIdValue);
        this.startCycle(); // Принудително стартиране

        // Също така, провери дали методът startCycle е правилно достъпен
        console.log('Методът startCycle е достъпен:', typeof this.startCycle === 'function');
        this.element.addEventListener('click', (event) => {
            console.log('Click event on modal cycle button detected.', event);
        });
    }

    async startCycle() {
        try {
            console.log('Започване на цикъл с URL-ите:', this.urlsValue);
            for (const url of this.urlsValue) {
                console.log('Предаване на URL:', url);
                await this.showOrderModal(url);
            }
        } catch (error) {
            console.error('Грешка при стартиране на цикъла:', error);
        }
    }

    async showOrderModal(url) {
        console.log('Показване на модал за URL:', url);

        // Получаване на контролера за модала
        const modalController = this.application.getControllerForElementAndIdentifier(
            document.querySelector('[data-controller="modal-form"]'),
            'modal-form'
        );

        if (modalController) {
            modalController.formUrlValue = url;
            await modalController.openModal();

            return new Promise((resolve) => {
                modalController.element.addEventListener('success', () => {
                    console.log('Формата е изпратена успешно');
                    resolve();
                });
            });
        } else {
            console.error('Не беше намерен контролер за модала.');
        }
    }
}
    */
// controllers/bulk_status_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        urls: Array,
        statusId: String,
        modalTitle: String 
    }

    connect() {
        console.log('bulk-status контролер е свързан.');
        console.log('Получени URL-и:', this.urlsValue);
        console.log('Получен Status ID:', this.statusIdValue);
        console.log('Получен modalTitle:', this.modalTitleValue);

        this.hasChanges = false;  // Добавяме променлива, която следи за промени
        this.currentUrlIndex = 0;
        this.startCycle();

        window.addEventListener('success', this.handleSuccess.bind(this));
    }

    handleSuccess() {
        console.log('Operation was successful.');
        this.hasChanges = true; // Може да актуализирате променливата при успешна операция
        this.showSuccessMessage(); // Показваме съобщение за успех
    }

    disconnect() {
        console.log('Removing event listeners.');
        window.removeEventListener('success', this.handleSuccess.bind(this));
    }

    startCycle() {
        if (this.currentUrlIndex < this.urlsValue.length) {
            const url = this.urlsValue[this.currentUrlIndex];
            
            console.log('Обработка на URL:', url);
            this.showOrderModal(url);
        } else {
            console.log('Всички поръчки са обработени.');
            console.log('hasChanges:', this.hasChanges)
            // Проверка дали всички URL адреси са обработени
        if (this.currentUrlIndex == this.urlsValue.length) {
            if (this.hasChanges) {  // Показваме съобщението само ако е направена промяна
                this.showSuccessMessage();
                setTimeout(() => {
                    window.location.href = "/order"; // Презареждане на страницата след успешна промяна
                }, 3000); // Презареждане след 3 секунди
            } else {
                // Ако няма промени, просто презареждаме страницата без да показваме съобщение за успех
                window.location.href = "/order";
            }
            }
        }
    }

    async showOrderModal(url) {
        const modalController = this.application.getControllerForElementAndIdentifier(
            document.querySelector('[data-controller="modal-form"]'),
            'modal-form'
        );

        if (modalController) {
            modalController.formUrlValue = url;
            modalController.modalTitleValue = this.modalTitleValue; // Добавяне на заглавието
            await modalController.openModal();

            modalController.element.addEventListener('hidden.bs.modal', () => {
                console.log('Модалът е затворен, преминаваме към следващия URL.');
                this.currentUrlIndex++;
                this.startCycle(); // Преминаваме към следващия URL
            }, { once: true });
        } else {
            console.error('Не беше намерен контролер за модала.');
        }
    }

    showSuccessMessage() {
        console.log('Attempting to show success message.');
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.display = 'block';
            
        } else {
            console.error('Element with id "successMessage" not found.');
        }
    }
}