/*document.addEventListener('DOMContentLoaded', function () {
    console.log('JavaScript зареден успешно.');
    var changeStatusButton = document.getElementById('changeStatusButton');


    // Проверка дали бутонът съществува
    if (changeStatusButton) {
    changeStatusButton.addEventListener('click', function () {
        console.log('Бутонът е натиснат.');
        var checkboxes = document.querySelectorAll('input[name="selectedOrders[]"]:checked');
        var selectedOrders = [];
        var statusId = null;

        checkboxes.forEach(function (checkbox) {
            selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
            statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
        });

        if (selectedOrders.length === 0) {
            alert('Моля, изберете поне една поръчка.');
            return;
        }

        // Създаване на динамичен формуляр
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = changeStatusButton.getAttribute('data-url');

        // Добавяне на скрити полета за избраните поръчки
        selectedOrders.forEach(function (orderId) {
            var orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'selectedOrders[]';
            orderInput.value = orderId; // Тук orderId идва от selectedOrders масива
            form.appendChild(orderInput);
        });

        // Добавяне на скрито поле за статуса
        var statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'statusId';
        statusInput.value = statusId;
        form.appendChild(statusInput);

        // Добавяне на формуляра към документа и изпращането му
        document.body.appendChild(form);
        form.submit();
    });
}
});
*/

/*document.addEventListener('turbo:load', function () {
    //викаме функцията safeEventListener за проверка дали съществува id=changeStatusButon
    // и ако съществува, ще се изпълни долния код, за да не ползваме IF
    safeEventListener('#changeStatusButton', 'click', function() {
        var checkboxes = document.querySelectorAll('input[name="selectedOrders[]"]:checked');
        console.log('масив поръчки:', checkboxes);
        var selectedOrders = [];
        var statusId = null;
        var modalTitle = null;

        checkboxes.forEach(function (checkbox) {
            selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
            statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
            modalTitle = checkbox.getAttribute('data-title');
        });
        console.log('selectedOrders:', selectedOrders);
        console.log('statusId:', statusId);
        console.log('modalTitle:', modalTitle);
        if (selectedOrders.length === 0) {
            alert('Моля, изберете поне една поръчка.');
            return;
        }

        // Генериране на URL-ите
        var urls = selectedOrders.map(orderId => `/status/order?orderId=${orderId}`);
        console.log('url:', urls);

        

        // Създаване на нов бутон с динамични атрибути
        var modalCycleButton = document.createElement('button');
        modalCycleButton.setAttribute('data-controller', 'bulk-status');
        modalCycleButton.setAttribute('data-bulk-status-urls-value', JSON.stringify(urls));
        modalCycleButton.setAttribute('data-bulk-status-status-id-value', statusId);
        modalCycleButton.setAttribute('data-bulk-status-modal-title-value', modalTitle);
        modalCycleButton.style.display = 'none'; // Скриваме бутона от потребителя


        document.body.appendChild(modalCycleButton);

        // Симулиране на клик върху новия бутон
        modalCycleButton.click();
    });
});
//функция за проверка дали даден елемент съществува/вместо да се ползва "if"
function safeEventListener(selector, event, handler) {
    const element = document.querySelector(selector);
    if (element) {
        element.addEventListener(event, handler);
    }
} */
    document.addEventListener('turbo:load', function () { 
        function setupBulkButton(buttonId, statusType) {
            console.log('STATUS TYPE:', statusType);
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function () {
                    var checkboxes = document.querySelectorAll(`input[name="selectedOrders_${statusType}[]"]:checked`);
                    var selectedOrders = [];
                    var statusId = null;
                    var modalTitle = null;
                    
                    checkboxes.forEach(function (checkbox) {
                        selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
                        statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
                        modalTitle = checkbox.getAttribute('data-title');
                    });
    
                    console.log(`selectedOrders (${statusType}):`, selectedOrders);
                    console.log(`statusId (${statusType}):`, statusId);
                    console.log(`modalTitle (${statusType}):`, modalTitle);
    
                    if (selectedOrders.length === 0) {
                        alert('Моля, изберете поне една поръчка.');
                        return;
                    }
                    console.log("STATUSTYPE:", statusType);
                    if (statusType=='status'){
                        var urls = selectedOrders.map(orderId => `/status/order?orderId=${orderId}`);
                        console.log(`if-url: (${statusType}):`, urls);
                    } else {
                    var urls = selectedOrders.map(orderId => `/status/${statusType}?orderId=${orderId}`);
                    console.log(`url (${statusType}):`, urls);
                    }    
                    var modalCycleButton = document.createElement('button');
                    modalCycleButton.setAttribute('data-controller', 'bulk-status');
                    modalCycleButton.setAttribute('data-bulk-status-urls-value', JSON.stringify(urls));
                    modalCycleButton.setAttribute('data-bulk-status-status-id-value', statusId);
                    modalCycleButton.setAttribute('data-bulk-status-modal-title-value', modalTitle);
                    modalCycleButton.style.display = 'none';
    
                    document.body.appendChild(modalCycleButton);
    
                    modalCycleButton.click();
                });
            } else {
                //console.error(`Button with id ${buttonId} not found.`);
            }
        }
    
        function updateButtonStates() {
            var statusCheckboxes = document.querySelectorAll('input[name="selectedOrders_status[]"]:checked');
            var glassCheckboxes = document.querySelectorAll('input[name="selectedOrders_glass[]"]:checked');
            
            var statusButton = document.getElementById('changeStatusButton');
            var glassButton = document.getElementById('changeStatusButtonGlass');
    
            if (statusButton && glassButton) {
                // Ако има избрани чекбоксове за статус, деактивирай бутоните за glass
                if (statusCheckboxes.length > 0) {
                    glassButton.disabled = true;
                } else {
                    glassButton.disabled = false;
                }
    
                // Ако има избрани чекбоксове за glass, деактивирай бутоните за статус
                if (glassCheckboxes.length > 0) {
                    statusButton.disabled = true;
                } else {
                    statusButton.disabled = false;
                }
            } else {
               // console.error('One or both buttons not found.');
            }
        }
    
        // Настройка на бутоните
        setupBulkButton('changeStatusButton', 'status');
        setupBulkButton('changeStatusButtonGlass', 'glass');
        setupBulkButton('changeStatusButtonDetail', 'detail');
        setupBulkButton('changeStatusButtonMosquito', 'mosquito');
        // Наблюдавай промените в чекбоксовете
        document.querySelectorAll('input[name="selectedOrders_status[]"], input[name="selectedOrders_glass[]"], input[name="selectedOrders_detail[]"], input[name="selectedOrders_mosquito[]"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateButtonStates);
        });
    
        // Инициализиране на състоянието на бутоните при зареждане на страницата
        updateButtonStates();
    });