import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,  
    }

    connect() {
        this.element.addEventListener('update-url', this.updateUrl.bind(this));
    }
    getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    updateUrl(event) {
        const button = event.currentTarget;
        const currentPage = button.getAttribute('data-current-page');
        let filterForm = button.getAttribute('data-filter-form');
        const totalPagesFromUrl = this.getQueryParam('totalPages');  // Вземаме totalPages от URL
        if(totalPagesFromUrl !==null && currentPage > totalPagesFromUrl){
            filterForm = true;
            console.log("filterForm:", filterForm);
        }
        const urlSearch = window.location.search;
        
        if(filterForm == true){
            this.urlValue = window.location.origin + '/order/?ajax=1' +urlSearch;
        } else{
            this.urlValue = window.location.origin + '/order/?ajax=1&page=' + currentPage;
        }
    }
    async refreshContent() {
        
        const response = await fetch(this.urlValue);
        const html = await response.text();
        this.contentTarget.innerHTML = html;
        // След като съдържанието е обновено, презареждаме страницата
        setTimeout(() => {
            location.reload();  // Презареждане на страницата
        }, 0);  // Може да добавим забавяне, ако е нужно
    }
    
}
