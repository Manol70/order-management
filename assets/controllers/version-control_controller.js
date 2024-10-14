import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        isFullVersion: Boolean
    };


    connect() {
        console.log("Connected to version controller");
        // Четене на състоянието от localStorage, ако съществува
        const savedVersion = localStorage.getItem('tableVersion') || 'full';
        this.isFullVersionValue = savedVersion === 'full';
        
        // Настройване на интерфейса според текущото състояние
        this.updateButton();
        this.updateTable();
    }
    
    toggle(event) {
        // Превключване на състоянието
        this.isFullVersionValue = !this.isFullVersionValue;
        
        // Запазване на състоянието в localStorage
        localStorage.setItem('tableVersion', this.isFullVersionValue ? 'full' : 'short');
        
        // Актуализиране на интерфейса
        this.updateButton();
        this.updateTable();
    }

    updateButton() {
        // Актуализиране на текста на бутона в зависимост от състоянието
        const button = this.element.querySelector('button');
        button.innerText = this.isFullVersionValue ? 'Към кратка версия' : 'Към пълната версия';
    }
    
    updateTable() {
        // Актуализиране на видимостта на колоните в таблицата
        if (this.isFullVersionValue) {
            document.querySelectorAll('.short-version').forEach(el => el.style.display = 'table-cell');
        } else {
            document.querySelectorAll('.short-version').forEach(el => el.style.display = 'none');
        }
    }


}