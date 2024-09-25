import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        urls: Array,
        statusId: String,
        modalTitle: String,
        
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
        //this.showSuccessMessage(); // Показваме съобщение за успех
    }

    disconnect() {
        console.log('Removing event listeners.');
        window.removeEventListener('success', this.handleSuccess.bind(this));
    }

    startCycle() {
        if (this.currentUrlIndex < this.urlsValue.length) {
            const url = this.urlsValue[this.currentUrlIndex];
            
            console.log('Обработка на URL:', url);
            const bulk = true;  // Задаваме `bulk` на true при множествени промени
            this.showOrderModal(url, bulk);
        } else {
                        
            console.log('Всички поръчки са обработени.');
            this.hasChanges = true;
            console.log('hasChanges:', this.hasChanges)
            // Проверка дали всички URL адреси са обработени
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
            console.log('bulkShowOrderModal:', modalController.bulkValue);
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