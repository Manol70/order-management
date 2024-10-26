import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        urls: Array,
        statusId: String,
        modalTitle: String,
        
    }

    connect() {
        this.hasChanges = false;  // Добавяме променлива, която следи за промени
        this.currentUrlIndex = 0;
        this.startCycle();

        window.addEventListener('success', this.handleSuccess.bind(this));
    }

    handleSuccess() {
        this.hasChanges = true; // Актуализиране на променливата при успешна операция
    }

    disconnect() {
        window.removeEventListener('success', this.handleSuccess.bind(this));
    }

    startCycle() {
        if (this.currentUrlIndex < this.urlsValue.length) {
            const url = this.urlsValue[this.currentUrlIndex];
            const bulk = true;  // Задаваме `bulk` на true при множествени промени
            this.showOrderModal(url, bulk);
        } else {
            this.hasChanges = true;
            if (this.currentUrlIndex == this.urlsValue.length) {
                if (this.hasChanges) {  // Показваме съобщението само ако е направена промяна
                    alert('Всички поръчки са обработени.'); 
                    this.showSuccessMessage();
                    setTimeout(() => {
                        window.location.href = "/order"; // Презареждане на страницата след успешна промяна
                    }, 0); // Презареждане след 0 секунди, може да се добави време за изчакване
                } else {
                // Ако няма промени, просто презареждаме страницата без да показваме съобщение за успех
                window.location.href = "/order";
                }
            }
        }
    }

    async showOrderModal(url, bulk) {
        const modalController = this.application.getControllerForElementAndIdentifier(
            document.querySelector('[data-controller="modal-form"]'),
            'modal-form'
        );
            if (modalController) {
                modalController.formUrlValue = url;
                modalController.bulkValue = bulk;
                modalController.modalTitleValue = this.modalTitleValue; // Добавяне на заглавието
                await modalController.openModal();

                modalController.element.addEventListener('hidden.bs.modal', () => {
                    this.currentUrlIndex++;
                    this.startCycle(); // Преминаваме към следващия URL
                }, { once: true });
            } else {
            }
    
    }


    showSuccessMessage() {
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.display = 'block';
        } else {
        }
    }
}