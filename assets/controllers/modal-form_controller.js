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
        console.log("BULK=", this.bulkValue);
        
    }
 
    async openModal(event) {
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);

        // Тук се задава новото заглавие
        this.modalTitleTarget.textContent = this.modalTitleValue || "Промяна на статус"; 
        console.log("BULK=", this.bulkValue);
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
        
        if (this.bulkValue) {
            console.log('Обработваме множество поръчки.');
            this.modal.hide(); 
        } else {
            console.log('Обработваме единична поръчка.');
            this.modal.hide(); 
        console.log('Dispatching success event.');       
        this.dispatch('success', { detail: { quality: 1 }})  //изпращане на събитие до контролери в същия DOM ,reload-content в случая
        
    }
        
     /*   this.modal.hide(); 
        console.log('Dispatching success event.');       
        this.dispatch('success', { detail: { quality: 1 }})  //изпращане на събитие до контролери в същия DOM ,reload-content в случая
       window.dispatchEvent(new CustomEvent('success'));  // Изпращане на глобално събитие
       */
    }
    modalHidden() {
        console.log('it was hidden!');
    }
}
