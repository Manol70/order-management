import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,  
    }

    connect() {
        this.element.addEventListener('update-url', this.updateUrl.bind(this));
       // const totalPagesFromUrl = this.getQueryParam('totalPages');  // Вземаме totalPages от URL
       // console.log('Инициализиран контролер с общ брой страници от URL:', totalPagesFromUrl);
    }

    getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    updateUrl(event) {
        const button = event.currentTarget;
        const currentPage = button.getAttribute('data-current-page');
        let filterForm = button.getAttribute('data-filter-form');
        console.log("FILTER:", filterForm);
        const totalPagesFromUrl = this.getQueryParam('totalPages');  // Вземаме totalPages от URL
        console.log('Инициализиран контролер с общ брой страници от URL:', totalPagesFromUrl);
        console.log("CURRENT:", currentPage);
        console.log("TOTALPAGES:", totalPagesFromUrl);   
        if(totalPagesFromUrl !==null && currentPage > totalPagesFromUrl){
            filterForm = true;
            console.log("filterForm:", filterForm);
            alert("Проверка на URL");
        }
        //this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
        const urlSearch = window.location.search;
        console.log("filterForm:", filterForm);
        
        if(filterForm == true){
            this.urlValue = window.location.origin + '/order/?ajax=1' +urlSearch;
            console.log("Updated URL1:", this.urlValue);
            alert("Проверка на URL1");
        } else{
            this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
            console.log("Updated URL2:", this.urlValue);
            alert("Проверка на URL2");
        }
        console.log("Updated URL:", this.urlValue);
    }
    async refreshContent() {
        
        console.log("URL:::", this.urlValue);
        //alert("Проверка на URL");  // Временно добавяне
        /*this.contentTarget.style.opacity = .2;*/
        const response = await fetch(this.urlValue);
        console.log('THIS URL:', this.response);
        const html = await response.text();
        this.contentTarget.innerHTML = html;
       /* this.contentTarget.style.opacity = 1;*/
        // След като съдържанието е обновено, презареждаме страницата
       /* setTimeout(() => {
            location.reload();  // Презареждане на страницата
        }, 0);  // Можеш да добавиш малко забавяне, ако е нужно
*/
       
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