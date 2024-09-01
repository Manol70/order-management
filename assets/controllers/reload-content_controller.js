import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
    }

    connect() {
        this.element.addEventListener('update-url', this.updateUrl.bind(this));
    }

    updateUrl(event) {
        const button = event.currentTarget;
        const currentPage = button.getAttribute('data-current-page');
        this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
        console.log("Updated URL:", this.urlValue);
    }
    async refreshContent(event) {
        
        console.log("URL:::", this.urlValue);
        //alert("Проверка на URL");  // Временно добавяне
        this.contentTarget.style.opacity = .2;
        const response = await fetch(this.urlValue);
        const html = await response.text();
        this.contentTarget.innerHTML = html;
        this.contentTarget.style.opacity = 1;
    }
}


/*import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    connect() {
        this.refreshContent();
    }

    async refreshContent() {
        const response = await fetch(this.urlValue);
        const data = await response.text();
        this.contentTarget.innerHTML = html;
       // this.element.innerHTML = data;
        
        // Изпращаме глобално събитие след успешния запис
        const event = new CustomEvent('record:saved', {
            bubbles: true
        });
        window.dispatchEvent(event);
    }
}*/