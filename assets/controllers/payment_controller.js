import { Controller } from '@hotwired/stimulus';
import { useDispatch } from 'stimulus-use';

export default class extends Controller {
    static targets = ['form', 'content'];


    connect() {
        useDispatch(this, {debug: true});
    }



    async submitForm(event) {
        event.preventDefault();
        
        const formData = new FormData(this.formTarget);
        
        const response = await fetch(this.formTarget.action, {
            method: this.formTarget.method,
            body: formData
        });
        this.dispatch('success');
        if (response.ok) {
            const jsonResponse = await response.json();
            if (jsonResponse.success) {
                const contentResponse = await fetch(this.data.get("reloadContentUrl"));
                const html = await contentResponse.text();
                // Обновяване на съдържанието на страницата
                document.getElementById('mainContent').innerHTML = html;

            } else {
                console.error('Payment failed');
            }
        } else {
            console.error('Error submitting form', response.status, response.statusText);
        }
    }
}