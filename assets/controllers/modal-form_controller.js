import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['modal', 'modalBody', 'modalTitle'];
    static values = {
        formUrl: String,
        modalTitle: String
    }
    modal = null;
    
    connect() {
        // 
    }
 
    async openModal(event) {
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);

        // Тук се задава новото заглавие
        this.modalTitleTarget.textContent = this.modalTitleValue || "Промяна на статус"; 
        
        this.modal.show();
        this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue); 
    }

    async submit(event) {
        const $form = $(this.modalBodyTarget).find('form');

        console.log('Form action URL:', $form.prop('action'));
        console.log('Serialized data:', $form.serialize());

        
        this.modalBodyTarget.innerHTML = await $.ajax({
            url: $form.prop('action'),
            method: $form.prop('method'),
            data: $form.serialize(),
        });
        
        this.modal.hide(); 
        console.log('Dispatching success event.');       
        this.dispatch('success', { detail: { quality: 1 }})  //изпращане на събитие до контролери в същия DOM ,reload-content в случая
       window.dispatchEvent(new CustomEvent('success'));  // Изпращане на глобално събитие
       
    }
    modalHidden() {
        console.log('it was hidden!');
    }
}


/*import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['modal', 'modalBody'];
    static values = {
        formUrl: String,
    }
    modal = null;
    
    connect() {
        this.modal = new Modal(this.modalTarget);
    }

    async openModal(event) {
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal.show();
        this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue);  
    }

    async submit(event) {
        const $form = $(this.modalBodyTarget).find('form');
        console.log('Form action URL:', $form.prop('action'));
        console.log('Serialized data:', $form.serialize());

        this.modalBodyTarget.innerHTML = await $.ajax({
            url: $form.prop('action'),
            method: $form.prop('method'),
            data: $form.serialize(),
        });

        // Изпращаме събитие за успешно изпращане на формата
        this.dispatch('form:submitted', { bubbles: true });

        // Затварянето на модала вече се управлява от `modal-cycle`
    }

    closeModal() {
        if (this.modal) {
            this.modal.hide();
        }
    }
}*/