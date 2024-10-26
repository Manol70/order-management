import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['modal', 'modalBody', 'modalTitle'];
    static values = {
        formUrl: String,
        modalTitle: String,
        bulk: Boolean
    }
    modal = null;
    
    connect() {
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
        this.modalBodyTarget.innerHTML = await $.ajax({
            url: $form.prop('action'),
            method: $form.prop('method'),
            data: $form.serialize(),
        });
        
        if (this.bulkValue) {
            this.modal.hide(); 
        } else {
            this.modal.hide(); 
        this.dispatch('success', { detail: { quality: 1 }})  //изпращане на събитие до контролери в същия DOM ,reload-content в случая
        
        }
    }

    modalHidden() {
    }
}
