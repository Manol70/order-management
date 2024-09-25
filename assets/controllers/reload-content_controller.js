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
        const filterForm = button.getAttribute('data-filter-form');
        console.log("FILTER:", filterForm);
        //this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
        const urlSearch = window.location.search;
        if(filterForm == true){
            this.urlValue = window.location.origin + '/order/?ajax=1' +urlSearch;
            console.log("Updated URL1:", this.urlValue);
        } else{
            this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
            console.log("Updated URL2:", this.urlValue);
        }
        console.log("Updated URL:", this.urlValue);
    }
    async refreshContent() {
        
        console.log("URL:::", this.urlValue);
        //alert("Проверка на URL");  // Временно добавяне
        this.contentTarget.style.opacity = .2;
        const response = await fetch(this.urlValue);
        console.log('THIS URL:', this.response);
        const html = await response.text();
        this.contentTarget.innerHTML = html;
        this.contentTarget.style.opacity = 1;
        // След като съдържанието е обновено, презареждаме страницата
        setTimeout(() => {
            location.reload();  // Презареждане на страницата
        }, 0);  // Можеш да добавиш малко забавяне, ако е нужно

       
    }

    
}
/*

import { Controller } from '@hotwired/stimulus';
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
        this.contentTarget.innerHTML = data;
       //this.element.innerHTML = data;
        
        // Изпращаме глобално събитие след успешния запис
        const event = new CustomEvent('record:saved', {
            bubbles: true
        });
        window.dispatchEvent(event);
    }
}
    */